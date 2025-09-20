# Autorisierungs-Dokumentation

Diese Dokumentation erklärt das Autorisierungssystem der APDE Backend-Anwendung und warum es auch bei Sanctum-Authentifizierung essentiell ist.

## Warum Autorisierung benötigt wird

### Authentifizierung vs. Autorisierung

- **Authentifizierung** (Sanctum): „Wer sind Sie?" - Verifiziert die Benutzeridentität
- **Autorisierung** (Policies): „Was dürfen Sie tun?" - Kontrolliert den Zugriff auf Ressourcen

### Sicherheitsrisiken ohne Autorisierung

Ohne ordnungsgemässe Autorisierung könnte jeder authentifizierte Benutzer:
- Kampagnen erstellen, aktualisieren oder löschen (auch mit `GUEST`-Rolle)
- Auf alle Prospekt-Daten ohne Einschränkungen zugreifen
- Landing Pages modifizieren
- Administrative Aktionen durchführen

## Autorisierungs-Architektur

Die Anwendung verwendet ein **policy-basiertes Autorisierungssystem** mit `Gate::authorize()` für maximale Flexibilität und Wartbarkeit:

### 1. Policy-basierte Autorisierung (Primäre Schicht)

```php
// app/Policies/CampaignPolicy.php
public function create(User $user): bool
{
    return in_array($user->role, [
        UserRole::ADMIN,
        UserRole::SUPER_ADMIN
    ]);
}

// app/Policies/ProspectPolicy.php (Nur SuperAdmin)
public function viewAny(User $user): bool
{
    return $user->role === UserRole::SUPER_ADMIN;
}
```

### 2. Controller-Level Autorisierungsprüfungen (Gate Facade)

```php
// app/Http/Controllers/Api/CampaignController.php
public function store(CampaignRequest $request): JsonResource
{
    Gate::authorize('create', Campaign::class);

    return Campaign::create($request->validated())->toResource();
}
```

### 3. Route-Level Authentifizierung (Sanctum)

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('landingpages', LandingpageController::class);
    Route::apiResource('prospects', ProspectController::class)->only(['index', 'show']);
});
```

## Warum Gate::authorize() Ansatz?

### Vorteile von Gate::authorize() gegenüber anderen Methoden

1. **Explizit und klar**: `Gate::authorize()` macht Autorisierungsabsichten explizit
2. **Konsistent**: Gleicher Ansatz in der gesamten Anwendung verwendet
3. **Automatische Ausnahmebehandlung**: Wirft `AuthorizationException` automatisch
4. **Überall verfügbar**: Kann in jedem Kontext verwendet werden, nicht nur in Controllern
5. **Laravel-Standard**: Folgt Laravels empfohlenen Patterns

### Beispiel: Verschiedene Regeln für verschiedene Aktionen

```php
// CampaignPolicy.php
public function viewAny(User $user): bool
{
    // Alle authentifizierten Benutzer können Kampagnen anzeigen
    return in_array($user->role, [
        UserRole::USER,
        UserRole::ADMIN,
        UserRole::SUPER_ADMIN
    ]);
}

public function create(User $user): bool
{
    // Nur Admins können Kampagnen erstellen
    return in_array($user->role, [
        UserRole::ADMIN,
        UserRole::SUPER_ADMIN
    ]);
}

public function forceDelete(User $user, Campaign $campaign): bool
{
    // Nur Super-Admins können permanent löschen
    return $user->role === UserRole::SUPER_ADMIN;
}
```

## Benutzerrollen und Berechtigungen

### Rollen-Hierarchie

```php
enum UserRole: string
{
    case GUEST = 'guest';        // Eingeschränkter Lesezugriff
    case USER = 'user';          // Standard-Benutzerzugriff
    case ADMIN = 'admin';        // Administrativer Zugriff
    case SUPER_ADMIN = 'super_admin'; // Vollständiger Systemzugriff
}
```

### Berechtigungs-Matrix

| Aktion | GUEST | USER | ADMIN | SUPER_ADMIN |
|--------|-------|------|-------|-------------|
| Prospekte anzeigen | ❌ | ❌ | ✅ | ✅ |
| Kampagnen anzeigen | ❌ | ✅ | ✅ | ✅ |
| Landing Pages anzeigen | ❌ | ✅ | ✅ | ✅ |
| LP anzeigen (öffentlich) | ✅ | ✅ | ✅ | ✅ |
| Prospekte erstellen | ❌ | ❌ | ❌ | ✅ |
| Kampagnen erstellen | ❌ | ❌ | ✅ | ✅ |
| Landing Pages erstellen | ❌ | ❌ | ✅ | ✅ |
| Prospekte aktualisieren | ❌ | ❌ | ❌ | ✅ |
| Kampagnen aktualisieren | ❌ | ❌ | ✅ | ✅ |
| Landing Pages aktualisieren | ❌ | ❌ | ✅ | ✅ |
| Prospekte löschen | ❌ | ❌ | ❌ | ✅ |
| Kampagnen löschen | ❌ | ❌ | ✅ | ✅ |
| Landing Pages löschen | ❌ | ❌ | ✅ | ✅ |
| Permanent löschen | ❌ | ❌ | ❌ | ✅ |

## Implementierungsdetails

### Policy-Registrierung

```php
// app/Models/Campaign.php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

final class Campaign extends Model
{
    use HasFactory, SoftDeletes, AuthorizesRequests;

    protected $policies = [
        Campaign::class => CampaignPolicy::class,
    ];
}
```

### Controller-Autorisierung (Gate Facade)

```php
// app/Http/Controllers/Api/CampaignController.php
use Illuminate\Support\Facades\Gate;

final class CampaignController extends Controller
{
    public function store(CampaignRequest $request): JsonResource
    {
        Gate::authorize('create', Campaign::class);

        return Campaign::create($request->validated())->toResource();
    }
}
```

### Form Request Autorisierung

```php
// app/Http/Requests/CampaignRequest.php
public function authorize(): bool
{
    return Auth::check(); // Nur Authentifizierung prüfen, Policies handhaben Autorisierung
}
```

## Strenge Prospekt-Sicherheit

### Nur-SuperAdmin-Zugriff

Prospekte enthalten sensible persönliche Daten und sind nur für SuperAdmin-Zugriff beschränkt:

```php
// app/Policies/ProspectPolicy.php
final class ProspectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::SUPER_ADMIN;
    }

    public function view(User $user, Prospect $prospect): bool
    {
        return $user->role === UserRole::SUPER_ADMIN;
    }

    // Alle anderen Methoden geben ebenfalls $user->role === UserRole::SUPER_ADMIN zurück
}
```

### Generische Filter-Autorisierung

Sogar die generischen Filter-Endpunkte erzwingen Prospekt-Autorisierung:

```php
// app/Http/Controllers/Api/GenericFilterController.php
public function filter(Request $request, string $model): ResourceCollection
{
    $modelClass = $this->resolveModel($model);

    // Zugriff auf das Model autorisieren
    Gate::authorize('viewAny', $modelClass);

    // ... Rest der Methode
}
```

## Token-Fähigkeiten (Sanctum)

Für granulare Berechtigungen jenseits von Rollen verwenden Sie Sanctum-Token-Fähigkeiten:

```php
// Token mit spezifischen Fähigkeiten erstellen
$token = $user->createToken('api-token', ['view-prospects', 'create-campaigns']);

// Fähigkeiten in Routen prüfen
Route::get('/cp-cookie', function () {
    return response()->json(App\Models\Campaign::all());
})->middleware(['abilities:view-cp']);
```

## Fehler-Responses

### 401 Nicht authentifiziert
```json
{
    "message": "Unauthenticated."
}
```

### 403 Verboten
```json
{
    "message": "This action is unauthorized."
}
```

## Best Practices

### 1. Einzige Quelle der Wahrheit
- Verwenden Sie Policies als primären Autorisierungsmechanismus
- Verwenden Sie `Gate::authorize()` konsistent in der gesamten Anwendung
- Halten Sie Autorisierungsregeln in Policies zentralisiert

### 2. Prinzip der geringsten Berechtigung
- Gewähren Sie nur minimal notwendige Berechtigungen
- Beginnen Sie mit restriktiven Policies
- Fügen Sie Berechtigungen nach Bedarf hinzu

### 3. Policy-Organisation
- Eine Policy pro Model
- Klare Methodennamen (viewAny, view, create, update, delete)
- Konsistente Rollen-Prüfungsmuster

### 4. Testen
- Testen Sie Policies unabhängig
- Testen Sie Autorisierung in Feature-Tests
- Mocken Sie Autorisierung in Unit-Tests

## Autorisierung testen

### Policy-Tests
```php
public function test_user_cannot_create_campaign()
{
    $user = User::factory()->create(['role' => UserRole::USER]);
    $policy = new CampaignPolicy();

    $this->assertFalse($policy->create($user));
}
```

### Feature-Tests
```php
public function test_super_admin_can_view_prospects()
{
    $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

    $this->actingAs($user)
        ->getJson('/api/prospects')
        ->assertStatus(200);
}

public function test_admin_cannot_view_prospects()
{
    $user = User::factory()->create(['role' => UserRole::ADMIN]);

    $this->actingAs($user)
        ->getJson('/api/prospects')
        ->assertStatus(403);
}
```

## Sicherheitsüberlegungen

### 1. Policy-Sicherheit
- Validieren Sie alle Policy-Methoden
- Testen Sie Grenzfälle
- Auditieren Sie Policy-Änderungen

### 2. Token-Sicherheit
- Verwenden Sie kurzlebige Token
- Implementieren Sie Token-Rotation
- Überwachen Sie Token-Nutzung

### 3. Rollen-Management
- Validieren Sie Rollenzuweisungen
- Implementieren Sie Rollen-Vererbung
- Auditieren Sie Rollen-Änderungen

### 4. Überwachung
- Protokollieren Sie Autorisierungsfehler
- Überwachen Sie Privilege-Escalation
- Verfolgen Sie ungewöhnliche Zugriffsmuster

## Fazit

Das policy-basierte Autorisierungssystem mit `Gate::authorize()` bietet:

1. **Saubere Architektur**: Einzelne Verantwortlichkeit für Autorisierungslogik
2. **Konsistenz**: Gleicher Autorisierungsansatz in der gesamten Anwendung
3. **Flexibilität**: Einfache Änderung von Berechtigungen ohne Routen-Änderungen
4. **Testbarkeit**: Policies sind einfach zu unit-testen
5. **Wartbarkeit**: Zentralisierte Autorisierungsregeln
6. **Skalierbarkeit**: Einfaches Hinzufügen neuer Models und Policies
7. **Sicherheit**: Strenge Zugriffskontrolle für sensible Daten wie Prospekte

Dieser Ansatz folgt Laravel Best Practices und bietet eine robuste Grundlage für Autorisierung, die mit den Anforderungen Ihrer Anwendung skaliert.
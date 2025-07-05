# Authorization Documentation

This document explains the authorization system implemented in the APDE backend application and why it's essential even with Sanctum authentication.

## Why Authorization is Needed

### Authentication vs Authorization

- **Authentication** (Sanctum): "Who are you?" - Verifies user identity
- **Authorization** (Policies): "What are you allowed to do?" - Controls access to resources

### Security Risks Without Authorization

Without proper authorization, any authenticated user could:
- Create, update, or delete campaigns (even with `GUEST` role)
- Access all prospects data without restrictions
- Modify landing pages
- Perform administrative actions

## Authorization Architecture

The application uses a **policy-based authorization system** with `Gate::authorize()` for maximum flexibility and maintainability:

### 1. Policy-Based Authorization (Primary Layer)

```php
// app/Policies/CampaignPolicy.php
public function create(User $user): bool
{
    return in_array($user->role, [
        UserRole::ADMIN,
        UserRole::SUPER_ADMIN
    ]);
}

// app/Policies/ProspectPolicy.php (SuperAdmin only)
public function viewAny(User $user): bool
{
    return $user->role === UserRole::SUPER_ADMIN;
}
```

### 2. Controller-Level Authorization Checks (Gate Facade)

```php
// app/Http/Controllers/Api/CampaignController.php
public function store(CampaignRequest $request): JsonResource
{
    Gate::authorize('create', Campaign::class);
    
    return Campaign::create($request->validated())->toResource();
}
```

### 3. Route-Level Authentication (Sanctum)

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('landingpages', LandingpageController::class);
    Route::apiResource('prospects', ProspectController::class)->only(['index', 'show']);
});
```

## Why Gate::authorize() Approach?

### Advantages of Gate::authorize() Over Other Methods

1. **Explicit and Clear**: `Gate::authorize()` makes authorization intentions explicit
2. **Consistent**: Same approach used throughout the application
3. **Automatic Exception Handling**: Throws `AuthorizationException` automatically
4. **Available Everywhere**: Can be used in any context, not just controllers
5. **Laravel Standard**: Follows Laravel's recommended patterns

### Example: Different Rules for Different Actions

```php
// CampaignPolicy.php
public function viewAny(User $user): bool
{
    // All authenticated users can view campaigns
    return in_array($user->role, [
        UserRole::USER,
        UserRole::ADMIN,
        UserRole::SUPER_ADMIN
    ]);
}

public function create(User $user): bool
{
    // Only admins can create campaigns
    return in_array($user->role, [
        UserRole::ADMIN,
        UserRole::SUPER_ADMIN
    ]);
}

public function forceDelete(User $user, Campaign $campaign): bool
{
    // Only super admins can permanently delete
    return $user->role === UserRole::SUPER_ADMIN;
}
```

## User Roles and Permissions

### Role Hierarchy

```php
enum UserRole: string
{
    case GUEST = 'guest';        // Limited read access
    case USER = 'user';          // Standard user access
    case ADMIN = 'admin';        // Administrative access
    case SUPER_ADMIN = 'super_admin'; // Full system access
}
```

### Permission Matrix

| Action | GUEST | USER | ADMIN | SUPER_ADMIN |
|--------|-------|------|-------|-------------|
| View Prospects | ❌ | ❌ | ✅ | ✅ |
| View Campaigns | ❌ | ✅ | ✅ | ✅ |
| View Landing Pages | ❌ | ✅ | ✅ | ✅ |
| Create Prospects | ❌ | ❌ | ❌ | ✅ |
| Create Campaigns | ❌ | ❌ | ✅ | ✅ |
| Create Landing Pages | ❌ | ❌ | ✅ | ✅ |
| Update Prospects | ❌ | ❌ | ❌ | ✅ |
| Update Campaigns | ❌ | ❌ | ✅ | ✅ |
| Update Landing Pages | ❌ | ❌ | ✅ | ✅ |
| Delete Prospects | ❌ | ❌ | ❌ | ✅ |
| Delete Campaigns | ❌ | ❌ | ✅ | ✅ |
| Delete Landing Pages | ❌ | ❌ | ✅ | ✅ |
| Force Delete | ❌ | ❌ | ❌ | ✅ |


## Implementation Details

### Policy Registration

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

### Controller Authorization (Gate Facade)

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

### Form Request Authorization

```php
// app/Http/Requests/CampaignRequest.php
public function authorize(): bool
{
    return Auth::check(); // Just check authentication, policies handle authorization
}
```

## Strict Prospect Security

### SuperAdmin-Only Access

Prospects contain sensitive personal data and are restricted to SuperAdmin access only:

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

    // All other methods also return $user->role === UserRole::SUPER_ADMIN
}
```

### Generic Filter Authorization

Even the generic filter endpoints enforce prospect authorization:

```php
// app/Http/Controllers/Api/GenericFilterController.php
public function filter(Request $request, string $model): ResourceCollection
{
    $modelClass = $this->resolveModel($model);
    
    // Authorize access to the model
    Gate::authorize('viewAny', $modelClass);
    
    // ... rest of the method
}
```

## Token Abilities (Sanctum)

For fine-grained permissions beyond roles, use Sanctum token abilities:

```php
// Create token with specific abilities
$token = $user->createToken('api-token', ['view-prospects', 'create-campaigns']);

// Check abilities in routes
Route::get('/movies', function () {
    return response()->json(App\Models\Movie::all());
})->middleware(['abilities:view-movies']);
```

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "This action is unauthorized."
}
```

## Best Practices

### 1. Single Source of Truth
- Use policies as the primary authorization mechanism
- Use `Gate::authorize()` consistently throughout the application
- Keep authorization rules centralized in policies

### 2. Principle of Least Privilege
- Grant minimum necessary permissions
- Start with restrictive policies
- Add permissions as needed

### 3. Policy Organization
- One policy per model
- Clear method names (viewAny, view, create, update, delete)
- Consistent role checking patterns

### 4. Testing
- Test policies independently
- Test authorization in feature tests
- Mock authorization in unit tests

## Testing Authorization

### Policy Tests
```php
public function test_user_cannot_create_campaign()
{
    $user = User::factory()->create(['role' => UserRole::USER]);
    $policy = new CampaignPolicy();
    
    $this->assertFalse($policy->create($user));
}
```

### Feature Tests
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

## Security Considerations

### 1. Policy Security
- Validate all policy methods
- Test edge cases
- Audit policy changes

### 2. Token Security
- Use short-lived tokens
- Implement token rotation
- Monitor token usage

### 3. Role Management
- Validate role assignments
- Implement role inheritance
- Audit role changes

### 4. Monitoring
- Log authorization failures
- Monitor privilege escalation
- Track unusual access patterns

## Conclusion

The policy-based authorization system with `Gate::authorize()` provides:

1. **Clean Architecture**: Single responsibility for authorization logic
2. **Consistency**: Same authorization approach throughout the application
3. **Flexibility**: Easy to modify permissions without changing routes
4. **Testability**: Policies are easy to unit test
5. **Maintainability**: Centralized authorization rules
6. **Scalability**: Easy to add new models and policies
7. **Security**: Strict access control for sensitive data like prospects

This approach follows Laravel best practices and provides a robust foundation for authorization that scales with your application's needs. 
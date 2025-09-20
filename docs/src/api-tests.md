# API Test Specification

## Übersicht

Diese Dokumentation enthält eine vollständige Testspezifikation für alle API-Endpunkte der APDE-Anwendung. Die Tests sind nach Funktionsbereichen gruppiert und enthalten sowohl positive als auch negative Testfälle.

## Testumgebung Setup

### Voraussetzungen
- Laravel-Anwendung läuft lokal oder auf Test-Server
- Database ist mit Test-Daten befüllt
- API-Token für authentifizierte Anfragen verfügbar

### Base URL
```
http://localhost:8000/api
```

### Authentication
Alle authentifizierten Endpunkte verwenden Laravel Sanctum mit Bearer Token:
```
Authorization: Bearer {token}
```

## Test-Kategorien

### 1. Authentication Tests

#### 1.1 Login Tests

**Test Case: T-AUTH-001 - Successful Login**
- **Endpoint:** `POST /api/login`
- **Input:**
  ```json
  {
    "email": "test@example.com",
    "password": "password"
  }
  ```
- **Expected Result:**
  - Status: 200
  - Response enthält token
  - Response enthält user object
- **Validation:** Token ist gültig für weitere API-Aufrufe

**Test Case: T-AUTH-002 - Invalid Credentials**
- **Endpoint:** `POST /api/login`
- **Input:**
  ```json
  {
    "email": "test@example.com",
    "password": "wrongpassword"
  }
  ```
- **Expected Result:**
  - Status: 401
  - Error message: "Invalid credentials"

**Test Case: T-AUTH-003 - Missing Email**
- **Endpoint:** `POST /api/login`
- **Input:**
  ```json
  {
    "password": "password"
  }
  ```
- **Expected Result:**
  - Status: 422
  - Validation error für email field

**Test Case: T-AUTH-004 - Missing Password**
- **Endpoint:** `POST /api/login`
- **Input:**
  ```json
  {
    "email": "test@example.com"
  }
  ```
- **Expected Result:**
  - Status: 422
  - Validation error für password field

#### 1.2 Logout Tests

**Test Case: T-AUTH-005 - Successful Logout**
- **Endpoint:** `POST /api/logout`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Token wird invalidiert

**Test Case: T-AUTH-006 - Logout Without Token**
- **Endpoint:** `POST /api/logout`
- **Expected Result:**
  - Status: 401
  - Unauthenticated error

#### 1.3 User Profile Tests

**Test Case: T-AUTH-007 - Get Current User**
- **Endpoint:** `GET /api/user`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - User object mit allen relevanten Feldern

**Test Case: T-AUTH-008 - Get User Without Token**
- **Endpoint:** `GET /api/user`
- **Expected Result:**
  - Status: 401
  - Unauthenticated error

### 2. Campaign Tests

#### 2.1 Campaign CRUD Tests

**Test Case: T-CAMP-001 - List All Campaigns**
- **Endpoint:** `GET /api/campaigns`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Array von campaign objects
  - Pagination meta data (falls implementiert)

**Test Case: T-CAMP-002 - Create Campaign**
- **Endpoint:** `POST /api/campaigns`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Input:**
  ```json
  {
    "name": "Test Campaign",
    "description": "Test Description",
    "status": "draft",
    "landingpage_id": 1
  }
  ```
- **Expected Result:**
  - Status: 201
  - Created campaign object
  - ID ist generiert

**Test Case: T-CAMP-003 - Create Campaign with Invalid Data**
- **Endpoint:** `POST /api/campaigns`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Input:**
  ```json
  {
    "description": "Missing name field"
  }
  ```
- **Expected Result:**
  - Status: 422
  - Validation errors

**Test Case: T-CAMP-004 - Get Single Campaign**
- **Endpoint:** `GET /api/campaigns/{id}`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Campaign object mit allen Details

**Test Case: T-CAMP-005 - Get Non-Existent Campaign**
- **Endpoint:** `GET /api/campaigns/99999`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 404
  - Not found error

**Test Case: T-CAMP-006 - Update Campaign**
- **Endpoint:** `PUT /api/campaigns/{id}`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Input:**
  ```json
  {
    "name": "Updated Campaign Name",
    "status": "active"
  }
  ```
- **Expected Result:**
  - Status: 200
  - Updated campaign object

**Test Case: T-CAMP-007 - Delete Campaign**
- **Endpoint:** `DELETE /api/campaigns/{id}`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 204
  - Campaign wird gelöscht

#### 2.2 Campaign Analytics Tests

**Test Case: T-CAMP-008 - Get Campaign Analytics**
- **Endpoint:** `GET /api/campaigns/{id}/analytics`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Analytics data object

**Test Case: T-CAMP-009 - Get Email Statistics**
- **Endpoint:** `GET /api/campaigns/{id}/send-emails/sent`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Email statistics object

#### 2.3 Campaign Email Tests

**Test Case: T-CAMP-010 - Send Campaign Emails**
- **Endpoint:** `POST /api/campaigns/{id}/send-emails`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Confirmation message

### 3. Landing Page Tests

#### 3.1 Landing Page CRUD Tests

**Test Case: T-LP-001 - List All Landing Pages**
- **Endpoint:** `GET /api/landingpages`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Array von landingpage objects

**Test Case: T-LP-002 - Create Landing Page**
- **Endpoint:** `POST /api/landingpages`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Input:**
  ```json
  {
    "title": "Test Landing Page",
    "content": "Landing page content",
    "template": "default"
  }
  ```
- **Expected Result:**
  - Status: 201
  - Created landingpage object

**Test Case: T-LP-003 - Get Single Landing Page**
- **Endpoint:** `GET /api/landingpages/{id}`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Landing page object

**Test Case: T-LP-004 - Update Landing Page**
- **Endpoint:** `PUT /api/landingpages/{id}`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Input:**
  ```json
  {
    "title": "Updated Landing Page Title"
  }
  ```
- **Expected Result:**
  - Status: 200
  - Updated landingpage object

**Test Case: T-LP-005 - Delete Landing Page**
- **Endpoint:** `DELETE /api/landingpages/{id}`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 204

#### 3.2 Public Landing Page Tests

**Test Case: T-LP-006 - View Landing Page Publicly**
- **Endpoint:** `GET /api/cp/{identifier}`
- **Expected Result:**
  - Status: 200
  - Landing page content
  - Kein Authentication erforderlich

**Test Case: T-LP-007 - View Non-Existent Landing Page**
- **Endpoint:** `GET /api/cp/invalid-identifier`
- **Expected Result:**
  - Status: 404

### 4. Prospects Tests

#### 4.1 Prospects CRUD Tests

**Test Case: T-PROS-001 - List All Prospects**
- **Endpoint:** `GET /api/prospects`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Array von prospect objects

**Test Case: T-PROS-002 - Get Single Prospect**
- **Endpoint:** `GET /api/prospects/{id}`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Prospect object

**Test Case: T-PROS-003 - Get Non-Existent Prospect**
- **Endpoint:** `GET /api/prospects/99999`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 404

### 5. Filter & Search Tests

#### 5.1 Generic Filter Tests

**Test Case: T-FILTER-001 - Get Search Criteria**
- **Endpoint:** `GET /api/{model}/search-criteria`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Models zu testen:** campaigns, landingpages, prospects
- **Expected Result:**
  - Status: 200
  - Search criteria object

**Test Case: T-FILTER-002 - Filter with Valid Criteria**
- **Endpoint:** `GET /api/{model}/filter?field=name&value=test`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 200
  - Filtered results

**Test Case: T-FILTER-003 - Filter with Invalid Model**
- **Endpoint:** `GET /api/invalid-model/filter`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Expected Result:**
  - Status: 404 oder 422

### 6. Authorization Tests

#### 6.1 Protected Routes Tests

**Test Case: T-AUTH-009 - Access Protected Route Without Token**
- **Endpoint:** `GET /api/campaigns`
- **Expected Result:**
  - Status: 401
  - Unauthenticated error

**Test Case: T-AUTH-010 - Access Protected Route With Invalid Token**
- **Endpoint:** `GET /api/campaigns`
- **Headers:** `Authorization: Bearer invalid-token`
- **Expected Result:**
  - Status: 401
  - Unauthenticated error

#### 6.2 Special Permissions Tests

**Test Case: T-AUTH-011 - Access CP Cookie Endpoint**
- **Endpoint:** `GET /api/cp-cookie`
- **Headers:** `Authorization: Bearer {token_with_view_cp_ability}`
- **Expected Result:**
  - Status: 200

**Test Case: T-AUTH-012 - Access CP Cookie Without Permission**
- **Endpoint:** `GET /api/cp-cookie`
- **Headers:** `Authorization: Bearer {token_without_view_cp_ability}`
- **Expected Result:**
  - Status: 403
  - Insufficient permissions error

### 7. Documentation Tests

**Test Case: T-DOC-001 - Get OpenAPI Specification**
- **Endpoint:** `GET /api/docs/openapi`
- **Expected Result:**
  - Status: 200
  - Valid OpenAPI JSON

**Test Case: T-DOC-002 - Get OpenAPI YAML**
- **Endpoint:** `GET /api/docs/openapi/openapi.yaml`
- **Expected Result:**
  - Status: 200
  - Valid OpenAPI YAML

### 8. Error Handling Tests

#### 8.1 HTTP Error Codes

**Test Case: T-ERR-001 - Method Not Allowed**
- **Endpoint:** `PUT /api/login` (nur POST erlaubt)
- **Expected Result:**
  - Status: 405
  - Method not allowed error

**Test Case: T-ERR-002 - Invalid JSON**
- **Endpoint:** `POST /api/campaigns`
- **Headers:** `Authorization: Bearer {valid_token}`, `Content-Type: application/json`
- **Input:** `{invalid json}`
- **Expected Result:**
  - Status: 400
  - JSON parse error

**Test Case: T-ERR-003 - Missing Content-Type**
- **Endpoint:** `POST /api/campaigns`
- **Headers:** `Authorization: Bearer {valid_token}`
- **Input:** Form data statt JSON
- **Expected Result:**
  - Status: 415 oder 422

### 9. Performance Tests

**Test Case: T-PERF-001 - Response Time**
- **Endpoint:** Alle GET endpoints
- **Expected Result:**
  - Response time < 500ms für einfache queries
  - Response time < 2s für komplexe queries

**Test Case: T-PERF-002 - Large Dataset Handling**
- **Endpoint:** `GET /api/campaigns` mit vielen Campaigns
- **Expected Result:**
  - Pagination funktioniert korrekt
  - Response time bleibt akzeptabel

## Test-Durchführung

### Manuelle Tests

#### Vorbereitung
1. Erstelle Test-User: `php artisan tinker` → User factory
2. Erstelle Test-Daten: Campaigns, Landing Pages, Prospects
3. Generiere API-Token für Authentication

#### Test-Ausführung
1. Führe Tests in der angegebenen Reihenfolge aus
2. Dokumentiere alle Abweichungen von erwarteten Ergebnissen
3. Überprüfe Response-Zeiten
4. Validiere JSON-Schema der Responses

### Automatisierte Tests mit Postman

#### Collection Import
1. Importiere `docs/APDE.postman_collection.json`
2. Setze Environment-Variablen:
   - `base_url`: http://localhost:8000
   - `api_token`: {generated_token}

#### Test Runner
```bash
# Mit Newman (CLI)
npm install -g newman
newman run docs/APDE.postman_collection.json -e environment.json
```

### Pest/PHPUnit Tests

#### Bestehende Tests ausführen
```bash
# Alle Tests
php artisan test

# Nur Feature Tests
php artisan test tests/Feature

# Spezifische Test-Klasse
php artisan test tests/Feature/ApiTest.php

# Mit Coverage
php artisan test --coverage
```

#### Neue Tests erstellen
```bash
# Feature Test für API
php artisan make:test --pest ApiCampaignTest

# Unit Test für Controller
php artisan make:test --pest --unit CampaignControllerTest
```

### Test-Daten Management

#### Seeders verwenden
```bash
# Database mit Test-Daten befüllen
php artisan db:seed

# Specific Seeder
php artisan db:seed --class=CampaignSeeder
```

#### Factories verwenden
```php
// In Tests oder Tinker
$campaigns = Campaign::factory(10)->create();
$users = User::factory(5)->create();
```

### Monitoring und Logging

#### Laravel Logs
```bash
# Logs während Tests verfolgen
tail -f storage/logs/laravel.log
```

#### Query Logging
```php
// In Test-Setup
DB::listen(function ($query) {
    Log::info($query->sql, $query->bindings);
});
```

## Test-Berichterstattung

### Test Results Template

```
Test Execution Report
=====================

Date: {date}
Environment: {environment}
Tester: {name}

Summary:
- Total Tests: {total}
- Passed: {passed}
- Failed: {failed}
- Skipped: {skipped}

Failed Tests:
{list_of_failed_tests_with_details}

Performance Issues:
{list_of_slow_endpoints}

Recommendations:
{improvements_and_fixes}
```

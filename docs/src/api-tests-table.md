# API Test Specification - Tabular Format

## Test Cases Overview

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-AUTH-001 | Authentication | Successful Login | POST | /api/login | No | 200 | Valid credentials should return token and user object |
| T-AUTH-002 | Authentication | Invalid Credentials | POST | /api/login | No | 401 | Wrong password should return error |
| T-AUTH-003 | Authentication | Missing Email | POST | /api/login | No | 422 | Missing email should return validation error |
| T-AUTH-004 | Authentication | Missing Password | POST | /api/login | No | 422 | Missing password should return validation error |
| T-AUTH-005 | Authentication | Successful Logout | POST | /api/logout | Yes | 200 | Valid token logout should invalidate token |
| T-AUTH-006 | Authentication | Logout Without Token | POST | /api/logout | No | 401 | Logout without token should return error |
| T-AUTH-007 | Authentication | Get Current User | GET | /api/user | Yes | 200 | Valid token should return user profile |
| T-AUTH-008 | Authentication | Get User Without Token | GET | /api/user | No | 401 | No token should return unauthenticated error |
| T-AUTH-009 | Authorization | Access Protected Without Token | GET | /api/campaigns | No | 401 | Protected route without token should fail |
| T-AUTH-010 | Authorization | Access Protected Invalid Token | GET | /api/campaigns | Invalid | 401 | Invalid token should fail |
| T-AUTH-011 | Authorization | Access CP Cookie With Permission | GET | /api/cp-cookie | Special | 200 | Token with view-cp ability should succeed |
| T-AUTH-012 | Authorization | Access CP Cookie Without Permission | GET | /api/cp-cookie | Limited | 403 | Token without view-cp ability should fail |

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-CAMP-001 | Campaign | List All Campaigns | GET | /api/campaigns | Yes | 200 | Should return array of campaigns with pagination |
| T-CAMP-002 | Campaign | Create Campaign | POST | /api/campaigns | Yes | 201 | Valid data should create new campaign |
| T-CAMP-003 | Campaign | Create Invalid Campaign | POST | /api/campaigns | Yes | 422 | Invalid data should return validation errors |
| T-CAMP-004 | Campaign | Get Single Campaign | GET | /api/campaigns/{id} | Yes | 200 | Valid ID should return campaign details |
| T-CAMP-005 | Campaign | Get Non-Existent Campaign | GET | /api/campaigns/99999 | Yes | 404 | Invalid ID should return not found |
| T-CAMP-006 | Campaign | Update Campaign | PUT | /api/campaigns/{id} | Yes | 200 | Valid data should update campaign |
| T-CAMP-007 | Campaign | Delete Campaign | DELETE | /api/campaigns/{id} | Yes | 204 | Valid ID should delete campaign |
| T-CAMP-008 | Campaign | Get Campaign Analytics | GET | /api/campaigns/{id}/analytics | Yes | 200 | Should return analytics data |
| T-CAMP-009 | Campaign | Get Email Statistics | GET | /api/campaigns/{id}/send-emails/sent | Yes | 200 | Should return email statistics |
| T-CAMP-010 | Campaign | Send Campaign Emails | POST | /api/campaigns/{id}/send-emails | Yes | 200 | Should send emails and return confirmation |

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-LP-001 | Landing Page | List All Landing Pages | GET | /api/landingpages | Yes | 200 | Should return array of landing pages |
| T-LP-002 | Landing Page | Create Landing Page | POST | /api/landingpages | Yes | 201 | Valid data should create new landing page |
| T-LP-003 | Landing Page | Get Single Landing Page | GET | /api/landingpages/{id} | Yes | 200 | Valid ID should return landing page |
| T-LP-004 | Landing Page | Update Landing Page | PUT | /api/landingpages/{id} | Yes | 200 | Valid data should update landing page |
| T-LP-005 | Landing Page | Delete Landing Page | DELETE | /api/landingpages/{id} | Yes | 204 | Valid ID should delete landing page |
| T-LP-006 | Landing Page | View Public Landing Page | GET | /api/cp/{identifier} | No | 200 | Public access to landing page content |
| T-LP-007 | Landing Page | View Invalid Public Landing Page | GET | /api/cp/invalid | No | 404 | Invalid identifier should return not found |

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-PROS-001 | Prospects | List All Prospects | GET | /api/prospects | Yes | 200 | Should return array of prospects |
| T-PROS-002 | Prospects | Get Single Prospect | GET | /api/prospects/{id} | Yes | 200 | Valid ID should return prospect details |
| T-PROS-003 | Prospects | Get Non-Existent Prospect | GET | /api/prospects/99999 | Yes | 404 | Invalid ID should return not found |

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-FILTER-001 | Filter | Get Search Criteria | GET | /api/{model}/search-criteria | Yes | 200 | Should return available search criteria |
| T-FILTER-002 | Filter | Filter Valid Criteria | GET | /api/{model}/filter | Yes | 200 | Valid filter should return filtered results |
| T-FILTER-003 | Filter | Filter Invalid Model | GET | /api/invalid/filter | Yes | 404/422 | Invalid model should return error |

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-DOC-001 | Documentation | Get OpenAPI JSON | GET | /api/docs/openapi | No | 200 | Should return valid OpenAPI specification |
| T-DOC-002 | Documentation | Get OpenAPI YAML | GET | /api/docs/openapi/openapi.yaml | No | 200 | Should return valid OpenAPI YAML |

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-ERR-001 | Error Handling | Method Not Allowed | PUT | /api/login | No | 405 | Wrong HTTP method should return error |
| T-ERR-002 | Error Handling | Invalid JSON | POST | /api/campaigns | Yes | 400 | Malformed JSON should return parse error |
| T-ERR-003 | Error Handling | Missing Content-Type | POST | /api/campaigns | Yes | 415/422 | Form data instead of JSON should fail |

| Test ID | Category | Test Name | Method | Endpoint | Auth Required | Expected Status | Description |
|---------|----------|-----------|--------|----------|---------------|-----------------|-------------|
| T-PERF-001 | Performance | Response Time Simple | GET | All GET endpoints | Varies | 200 | Response time should be < 500ms |
| T-PERF-002 | Performance | Response Time Complex | GET | Complex queries | Yes | 200 | Response time should be < 2s |
| T-PERF-003 | Performance | Large Dataset | GET | /api/campaigns | Yes | 200 | Pagination should work with many records |

## Detailed Test Data

### Authentication Test Data

| Test ID | Input Data | Headers | Expected Response |
|---------|------------|---------|-------------------|
| T-AUTH-001 | `{"email": "test@example.com", "password": "password"}` | Content-Type: application/json | `{"token": "...", "user": {...}}` |
| T-AUTH-002 | `{"email": "test@example.com", "password": "wrong"}` | Content-Type: application/json | `{"error": "Invalid credentials"}` |
| T-AUTH-003 | `{"password": "password"}` | Content-Type: application/json | `{"errors": {"email": ["required"]}}` |
| T-AUTH-004 | `{"email": "test@example.com"}` | Content-Type: application/json | `{"errors": {"password": ["required"]}}` |
| T-AUTH-005 | - | Authorization: Bearer {token} | `{"message": "Logged out"}` |
| T-AUTH-006 | - | - | `{"error": "Unauthenticated"}` |
| T-AUTH-007 | - | Authorization: Bearer {token} | `{"id": 1, "email": "...", ...}` |
| T-AUTH-008 | - | - | `{"error": "Unauthenticated"}` |

### Campaign Test Data

| Test ID | Input Data | Headers | Expected Response |
|---------|------------|---------|-------------------|
| T-CAMP-001 | - | Authorization: Bearer {token} | `{"data": [...], "meta": {...}}` |
| T-CAMP-002 | `{"name": "Test Campaign", "description": "Test", "status": "draft", "landingpage_id": 1}` | Authorization: Bearer {token} | `{"id": 1, "name": "Test Campaign", ...}` |
| T-CAMP-003 | `{"description": "Missing name"}` | Authorization: Bearer {token} | `{"errors": {"name": ["required"]}}` |
| T-CAMP-004 | - | Authorization: Bearer {token} | `{"id": 1, "name": "...", ...}` |
| T-CAMP-005 | - | Authorization: Bearer {token} | `{"error": "Not found"}` |
| T-CAMP-006 | `{"name": "Updated Name", "status": "active"}` | Authorization: Bearer {token} | `{"id": 1, "name": "Updated Name", ...}` |
| T-CAMP-007 | - | Authorization: Bearer {token} | - (204 No Content) |

### Landing Page Test Data

| Test ID | Input Data | Headers | Expected Response |
|---------|------------|---------|-------------------|
| T-LP-001 | - | Authorization: Bearer {token} | `{"data": [...]}` |
| T-LP-002 | `{"title": "Test LP", "content": "Content", "template": "default"}` | Authorization: Bearer {token} | `{"id": 1, "title": "Test LP", ...}` |
| T-LP-003 | - | Authorization: Bearer {token} | `{"id": 1, "title": "...", ...}` |
| T-LP-004 | `{"title": "Updated Title"}` | Authorization: Bearer {token} | `{"id": 1, "title": "Updated Title", ...}` |
| T-LP-005 | - | Authorization: Bearer {token} | - (204 No Content) |
| T-LP-006 | - | - | Landing page HTML content |
| T-LP-007 | - | - | `{"error": "Not found"}` |

## Test Execution Instructions

### Manual Testing Steps

1. **Setup Environment**
   - Start Laravel application
   - Seed database with test data
   - Generate API tokens for authentication

2. **Execute Tests**
   - Follow test order by category
   - Document all deviations from expected results
   - Measure response times
   - Validate JSON schemas

3. **Report Results**
   - Use provided template for documentation
   - Include performance metrics
   - List all failed tests with details

### Automated Testing

#### Postman Collection
```bash
newman run docs/APDE.postman_collection.json --environment test-env.json
```

#### Pest/PHPUnit Tests
```bash
php artisan test --filter=Api
php artisan test tests/Feature/ApiTest.php
```

### Performance Benchmarks

| Endpoint Category | Max Response Time | Notes |
|------------------|-------------------|-------|
| Simple GET requests | 500ms | Single record retrieval |
| Complex queries | 2000ms | Filtered/paginated results |
| POST/PUT operations | 1000ms | Database write operations |
| DELETE operations | 500ms | Simple deletions |

### Error Code Reference

| HTTP Code | Usage | Test Cases |
|-----------|-------|------------|
| 200 | Successful GET/PUT | T-AUTH-001, T-CAMP-001, etc. |
| 201 | Successful POST (Create) | T-CAMP-002, T-LP-002 |
| 204 | Successful DELETE | T-CAMP-007, T-LP-005 |
| 400 | Bad Request (Invalid JSON) | T-ERR-002 |
| 401 | Unauthenticated | T-AUTH-002, T-AUTH-006, etc. |
| 403 | Forbidden | T-AUTH-012 |
| 404 | Not Found | T-CAMP-005, T-LP-007, etc. |
| 405 | Method Not Allowed | T-ERR-001 |
| 415 | Unsupported Media Type | T-ERR-003 |
| 422 | Validation Error | T-AUTH-003, T-CAMP-003, etc. |

## Test Result Template

| Test ID | Status | Response Time | Notes | Tester | Date |
|---------|--------|---------------|-------|--------|------|
| T-AUTH-001 | PASS | 245ms | - | - | - |
| T-AUTH-002 | PASS | 198ms | - | - | - |
| T-CAMP-001 | FAIL | 650ms | Too slow | - | - |
| ... | ... | ... | ... | ... | ... |

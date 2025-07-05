# APDE API Documentation

This directory contains the OpenAPI/Swagger documentation for the APDE (Application for Prospect Data Exchange) API.

## Files

- `openapi.yaml` - The main OpenAPI 3.0.3 specification file containing all API endpoints, schemas, and examples

## Local Viewing Options

You have **2 different ways** to view the API documentation:

### Option 1: Local View (Recommended)
- **URL**: `http://localhost:8000/docs/openapi/`
- **Method**: Uses Laravel Blade view at `resources/views/docs/openapi/index.blade.php`
- **Benefits**: Clean separation, easy to maintain and customize

### Option 2: Raw OpenAPI Specification
- **URL**: `http://localhost:8000/docs/openapi/openapi.yaml`
- **Method**: Serves the YAML file directly
- **Benefits**: Can be imported into other tools (Postman, Swagger Editor, etc.)

## External Viewing Options

You can also use the OpenAPI specification with external tools:

### Swagger Editor
1. Go to [Swagger Editor](https://editor.swagger.io/)
2. Copy the contents of `openapi.yaml`
3. Paste into the editor to view and edit

### Postman
1. Open Postman
2. Click "Import"
3. Select "Link" and paste: `http://localhost:8000/docs/openapi/openapi.yaml`
4. Or select "File" and upload the `openapi.yaml` file

## API Overview

The APDE API provides the following main functionalities:

### Authentication
- **POST** `/login` - User authentication
- **POST** `/logout` - User logout (requires authentication)

### Campaigns
- **GET** `/campaigns` - List campaigns
- **POST** `/campaigns` - Create campaign
- **GET** `/campaigns/{id}` - Get campaign by ID
- **PUT** `/campaigns/{id}` - Update campaign
- **DELETE** `/campaigns/{id}` - Delete campaign

### Landing Pages
- **GET** `/landingpages` - List landing pages
- **POST** `/landingpages` - Create landing page
- **GET** `/landingpages/{id}` - Get landing page by ID
- **PUT** `/landingpages/{id}` - Update landing page
- **DELETE** `/landingpages/{id}` - Delete landing page
- **GET** `/lp/{identifier}` - Get landing page by identifier (public endpoint)

### Prospects
- **GET** `/prospects` - List prospects
- **GET** `/prospects/{id}` - Get prospect by ID

### Generic Filters
- **GET** `/{model}/filter` - Filter model data (currently supports prospects)
- **GET** `/{model}/search-criteria` - Get available search criteria

### Special Endpoints
- **GET** `/cp-cookie` - Get campaigns with CP ability (requires specific permissions)

## Authentication

The API uses Laravel Sanctum for authentication. Most endpoints require a Bearer token obtained from the login endpoint.

**Example:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password123"}'
```

**Response:**
```json
{
  "token": "1|abc123def456..."
}
```

**Using the token:**
```bash
curl -X GET http://localhost:8000/api/campaigns \
  -H "Authorization: Bearer 1|abc123def456..."
```

## Data Models

### Prospect
Contains detailed information about prospects including:
- Personal information (name, email, phone, age, etc.)
- Physical attributes (height, weight, eye color, hair color, etc.)
- Address information
- Data source (ERP or Kueba)

### Campaign
Represents marketing campaigns with:
- Title and description
- Status (draft, active, paused, completed)
- Start and end dates
- Prospect filtering criteria
- Associated landing page

### Landing Page
Contains landing page configurations:
- Title, slug, and headlines
- Page sections (flexible content structure)
- Form field configurations
- Associated campaign

## Filtering and Search

The API provides flexible filtering capabilities:

### Prospect Filtering
You can filter prospects by various criteria:
- `source` - Data source (erp, kueba)
- `gender` - Gender
- `age_min` / `age_max` - Age range
- `blood_group` - Blood group
- `eye_color` - Eye color
- `hair_color` - Hair color
- `address.city` - City
- `address.state` - State
- `address.country` - Country
- `address.plz` - Postal code
- `address.latitude` / `address.longitude` - Geographic coordinates

**Example:**
```bash
curl -X GET "http://localhost:8000/api/prospects/filter?source=erp&age_min=18&age_max=65&gender=male" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Pagination

List endpoints return paginated results with metadata:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 10,
    "to": 10,
    "total": 50
  },
  "links": {
    "first": "http://localhost:8000/api/prospects?page=1",
    "last": "http://localhost:8000/api/prospects?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/prospects?page=2"
  }
}
```

## Error Handling

The API returns appropriate HTTP status codes and error messages:

- `200` - Success
- `201` - Created
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error

**Error Response Format:**
```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Field-specific error message"]
  }
}
```

## Development

To update the API documentation:

1. Modify the `openapi.yaml` file
2. Test the changes using one of the viewing options above
3. Update this README if necessary
4. Commit the changes to version control

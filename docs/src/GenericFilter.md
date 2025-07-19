# GenericFilter System Documentation

## Overview

The GenericFilter system is a flexible, reusable filtering solution for Laravel applications that provides dynamic filtering capabilities across different models. It consists of a trait (`HasFilterable`), a controller (`GenericFilterController`), and supporting infrastructure that allows models to be easily made filterable with minimal configuration.

The system is specifically designed to work with MongoDB using the `mongodb/laravel` package and provides comprehensive filtering capabilities for both simple and complex data structures.

## System Architecture

```mermaid
graph TB
    subgraph "Client Layer"
        Client[API Client]
    end
    
    subgraph "API Layer"
        Route[Route: /api/{model}/filter]
        Controller[GenericFilterController]
    end
    
    subgraph "Model Layer"
        Model[Filterable Model]
        Trait[HasFilterable Trait]
        Casts[Model Casts]
    end
    
    subgraph "Database Layer"
        MongoDB[(MongoDB)]
        Indexes[Database Indexes]
    end
    
    Client --> Route
    Route --> Controller
    Controller --> Model
    Model --> Trait
    Model --> Casts
    Trait --> MongoDB
    MongoDB --> Indexes
    
    style Controller fill:#e1f5fe
    style Trait fill:#f3e5f5
    style MongoDB fill:#e8f5e8
```

## Filter Processing Flow

```mermaid
sequenceDiagram
    participant Client as API Client
    participant Route as /api/{model}/filter
    participant Controller as GenericFilterController
    participant Model as Filterable Model
    participant Trait as HasFilterable Trait
    participant DB as MongoDB

    Client->>Route: GET /api/{model}/filter?gender=male&min_age=25
    Route->>Controller: filter($model)
    
    Controller->>Controller: resolveModel($model)
    Note over Controller: Validates model exists and is filterable
    
    Controller->>Model: scopeApplyFilters($query, $filters)
    Model->>Trait: scopeApplyFilters()
    
    loop For each filter parameter
        Trait->>Trait: Parse filter type
        Note over Trait: Determine if enum, range, or array filter
        
        alt Enum Filter
            Trait->>DB: where(field, '=', value)
        else Range Filter (min_)
            Trait->>DB: where(field, '>=', value)
        else Range Filter (max_)
            Trait->>DB: where(field, '<=', value)
        else Array Filter (_in)
            Trait->>DB: whereIn(field, values)
        else Array Filter (_not_in)
            Trait->>DB: whereNotIn(field, values)
        end
    end
    
    Trait->>DB: Execute query with pagination
    DB-->>Trait: Filtered results
    Trait-->>Model: Query with filters applied
    Model-->>Controller: Paginated collection
    Controller-->>Route: JSON response with data
    Route-->>Client: 200 OK + filtered data
```

## Search Criteria Generation Flow

```mermaid
sequenceDiagram
    participant Client as API Client
    participant Route as /api/{model}/search-criteria
    participant Controller as GenericFilterController
    participant Model as Filterable Model
    participant Trait as HasFilterable Trait
    participant DB as MongoDB

    Client->>Route: GET /api/{model}/search-criteria
    Route->>Controller: searchCriteria($model)
    
    Controller->>Controller: resolveModel($model)
    Controller->>Model: searchCriteria()
    Model->>Trait: searchCriteria()
    
    Trait->>Trait: getFilterableAttributes()
    Note over Trait: Get filter configuration from model
    
    loop For each filterable attribute
        Trait->>Trait: Determine filter type
        
        alt Enum Filter
            Trait->>DB: distinct(field)
            DB-->>Trait: Unique values
            Trait->>Trait: Format as array
        else Range Filter
            Trait->>DB: min(field), max(field)
            DB-->>Trait: Min/max values
            Trait->>Trait: Format as {min: x, max: y}
        end
    end
    
    Trait-->>Model: Complete search criteria
    Model-->>Controller: Search criteria object
    Controller-->>Route: JSON response
    Route-->>Client: 200 OK + search criteria
```

## Filter Types and Operations

```mermaid
graph LR
    subgraph "Filter Types"
        Enum[Enum Filters]
        Range[Range Filters]
        Array[Array Filters]
    end
    
    subgraph "Enum Operations"
        E1[= Exact Match]
        E2[in Multiple Values]
        E3[not_in Exclude Values]
    end
    
    subgraph "Range Operations"
        R1[min_ >= Value]
        R2[max_ <= Value]
    end
    
    subgraph "Array Operations"
        A1[_in Include Array]
        A2[_not_in Exclude Array]
    end
    
    Enum --> E1
    Enum --> E2
    Enum --> E3
    Range --> R1
    Range --> R2
    Array --> A1
    Array --> A2
    
    style Enum fill:#e3f2fd
    style Range fill:#f1f8e9
    style Array fill:#fff3e0
```

## Model Integration Example

```mermaid
classDiagram
    class Prospect {
        +string $id
        +string $external_id
        +string $first_name
        +string $last_name
        +string $email
        +string $gender
        +int $age
        +Carbon $birth_date
        +string $blood_group
        +float $height
        +float $weight
        +array $address
        +ProspectDataSource $source
        +getFilterableAttributes() array
        +scopeApplyFilters() void
        +searchCriteria() array
    }
    
    class HasFilterable {
        <<trait>>
        +scopeApplyFilters($query, $filters) void
        +searchCriteria() array
        -castFilterValue($field, $value) mixed
    }
    
    class GenericFilterController {
        +filter($model) ResourceCollection
        +searchCriteria($model) JsonResponse
        -resolveModel($slug) string
    }
    
    class MongoDB {
        <<database>>
        +prospects collection
        +personal_access_tokens collection
        +users collection
    }
    
    Prospect --> HasFilterable : uses
    GenericFilterController --> Prospect : filters
    Prospect --> MongoDB : stores data
```

## Data Flow for Complex Filtering

```mermaid
flowchart TD
    A[Client Request] --> B{Parse Query Parameters}
    B --> C[Extract Filters]
    C --> D[Validate Model]
    D --> E{Model Exists?}
    
    E -->|No| F[Return 404 Error]
    E -->|Yes| G[Get Filterable Attributes]
    
    G --> H[Process Each Filter]
    H --> I{Filter Type?}
    
    I -->|Enum| J[Apply Enum Filter]
    I -->|Range| K[Apply Range Filter]
    I -->|Array| L[Apply Array Filter]
    
    J --> M[Build Query]
    K --> M
    L --> M
    
    M --> N[Execute MongoDB Query]
    N --> O[Apply Pagination]
    O --> P[Format Response]
    P --> Q[Return JSON]
    
    style A fill:#e8f5e8
    style F fill:#ffebee
    style Q fill:#e8f5e8
    style J fill:#e3f2fd
    style K fill:#f1f8e9
    style L fill:#fff3e0
```

## Architecture

### Core Components

1. **HasFilterable Trait** (`app/Traits/HasFilterable.php`)
   - Provides the core filtering functionality
   - Implements `scopeApplyFilters()` method for query filtering
   - Implements `searchCriteria()` method for getting available filter options
   - Handles automatic value casting based on model casts
   - Supports MongoDB-specific query building
   - Includes dot notation support for nested fields
   - Prevents range operators on enum fields

2. **GenericFilterController** (`app/Http/Controllers/Api/GenericFilterController.php`)
   - Provides REST API endpoints for filtering
   - Handles model resolution and validation
   - Returns paginated results using Laravel's Resource Collections
   - Implements proper error handling for non-existent or non-filterable models
   - Uses Laravel's Gate authorization system
   - Supports only the `prospects` model currently

3. **Model Integration**
   - Models use the `HasFilterable` trait
   - Define filterable attributes via `getFilterableAttributes()` method
   - Support for different filter types (enum, range)
   - Currently only the `Prospect` model implements filtering

## Filter Types

### Enum Filters
- Used for fields with predefined sets of values
- Supports exact matching `=` and `in`/`not_in` operations
- Automatically generates available values from existing data
- Ideal for categorical data like gender, source, blood_group, etc.
- Range operators (`>=`, `<=`) are automatically skipped for enum fields

### Range Filters
- Used for numeric and date fields
- Supports `>=` (min_) and `<=` (max_) operations
- Automatically calculates min/max values from existing data
- Perfect for age, dates, measurements, and coordinates

## API Endpoints

### Filter Data
```
GET /api/{model}/filter
```

**Parameters:**
- Query parameters for filtering (see Filter Syntax below)
- Standard Laravel pagination parameters (`page`, `per_page`)

**Response:**
```json
{
  "data": [
    {
      "id": "507f1f77bcf86cd799439011",
      "gender": "male",
      "age": 25,
      "birthDate": "1998-05-15T00:00:00.000000Z",
      "image": "https://example.com/image.jpg",
      "bloodGroup": "A+",
      "height": 175.5,
      "weight": 70.2,
      "eyeColor": "brown",
      "hairColor": "black",
      "hairType": "straight",
      "address": {
        "city": "London",
        "state": "England",
        "country": "UK",
        "plz": "SW1A 1AA",
        "latitude": 51.5074,
        "longitude": -0.1278
      }
    }
  ],
  "current_page": 1,
  "per_page": 10,
  "total": 100,
  "last_page": 10
}
```

### Get Search Criteria
```
GET /api/{model}/search-criteria
```

**Response:**
```json
{
  "source": ["erp", "kueba"],
  "gender": ["male", "female"],
  "age": {
    "min": 18,
    "max": 85
  },
  "birth_date": {
    "min": "1940-01-01",
    "max": "2005-12-31"
  },
  "blood_group": ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"],
  "height": {
    "min": 150.0,
    "max": 200.0
  },
  "weight": {
    "min": 45.0,
    "max": 120.0
  },
  "eye_color": ["brown", "blue", "green", "hazel"],
  "hair_color": ["black", "brown", "blonde", "red"],
  "address.city": ["London", "Berlin", "Paris", "New York"],
  "address.state": ["England", "Berlin", "Île-de-France", "New York"],
  "address.country": ["UK", "Germany", "France", "USA"],
  "address.plz": {
    "min": "1000",
    "max": "99999"
  },
  "address.latitude": {
    "min": 35.0,
    "max": 70.0
  },
  "address.longitude": {
    "min": -180.0,
    "max": 180.0
  }
}
```

## Filter Syntax

### Basic Equality
```
?field=value
```

**Examples:**
```
?gender=male
?source=erp
?blood_group=A+
```

### Range Filters
```
?min_field=value    // >= value
?max_field=value    // <= value
```

**Examples:**
```
?min_age=25
?max_age=40
?min_height=170
?max_weight=80
```

### Array Filters
```
?field_in[]=value1&field_in[]=value2    // IN array
?field_not_in[]=value1&field_not_in[]=value2    // NOT IN array
```

**Examples:**
```
?gender_in[]=male&gender_in[]=female
?source_not_in[]=erp
?blood_group_in[]=A+&blood_group_in[]=O+
```

### Dot Notation Support
The system supports both underscore and dot notation for nested fields:
```
?address_city=London    // address.city field
?address.city=London    // Same field, dot notation
```

**Note:** PHP automatically converts dots to underscores in query parameters, so both notations work seamlessly. The system intelligently converts underscores back to dots when matching against filterable attributes.

## Implementation Example

### Making a Model Filterable

```php
<?php

namespace App\Models;

use App\Enums\ProspectDataSource;
use App\Traits\HasFilterable;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $external_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $gender
 * @property int|null $age
 * @property \Carbon\Carbon|null $birth_date
 * @property string|null $image
 * @property string|null $blood_group
 * @property float|null $height
 * @property float|null $weight
 * @property string|null $eye_color
 * @property string|null $hair_color
 * @property string|null $hair_type
 * @property array<string, mixed>|null $address
 * @property ProspectDataSource $source
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
final class Prospect extends Model
{
    use HasFilterable, SoftDeletes;

    protected $fillable = [
        'id',
        'external_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'age',
        'birth_date',
        'image',
        'blood_group',
        'height',
        'weight',
        'eye_color',
        'hair_color',
        'hair_type',
        'address',
        'source',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'age' => 'integer',
        'birth_date' => 'date',
        'height' => 'float',
        'weight' => 'float',
        // 'address' => 'array', // Laravel Serializes to JSON string and breaks dot "." notation
        'address.latitude' => 'float',
        'address.longitude' => 'float',
        'source' => ProspectDataSource::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return array<string, string>
     */
    public static function getFilterableAttributes(): array
    {
        return [
            'source' => 'enum',
            'gender' => 'enum',
            'age' => 'range',
            'birth_date' => 'range',
            'blood_group' => 'enum',
            'height' => 'range',
            'weight' => 'range',
            'eye_color' => 'enum',
            'hair_color' => 'enum',
            'address.city' => 'enum',
            'address.state' => 'enum',
            'address.country' => 'enum',
            'address.plz' => 'range',
            'address.latitude' => 'range',
            'address.longitude' => 'range',
        ];
    }
}
```

### Adding Model to Controller

```php
private function resolveModel(string $slug): string
{
    return match ($slug) {
        'prospects' => Prospect::class,
    };
}
```

## Usage Examples

### Basic Filtering
```
GET /api/prospects/filter?gender=male&source=erp
```

### Range Filtering
```
GET /api/prospects/filter?min_age=25&max_age=40
```

### Multiple Values
```
GET /api/prospects/filter?gender_in[]=male&gender_in[]=female
```

### Complex Filtering
```
GET /api/prospects/filter?source=erp&min_age=25&max_age=40&address_city=London
```

### Pagination
```
GET /api/prospects/filter?gender=male&page=2&per_page=20
```

### Nested Field Filtering
```
GET /api/prospects/filter?address_country=UK&min_address_latitude=50.0&max_address_latitude=60.0
```

### Combined Filters
```
GET /api/prospects/filter?source=kueba&min_age=30&max_age=50&blood_group_in[]=A+&blood_group_in[]=O+&address_city=Berlin
```

## Value Casting

The system automatically casts filter values based on the model's `$casts` property:

- **integer/int**: Numeric values cast to integers
- **float/double**: Numeric values cast to floats
- **boolean/bool**: String values cast to booleans
- **date/datetime**: String/numeric values parsed to Carbon instances
- **Enums**: Values cast using the enum's `from()` method

### Casting Examples

```php
// In the model's $casts property
protected $casts = [
    'age' => 'integer',
    'birth_date' => 'date',
    'height' => 'float',
    'weight' => 'float',
    'address.latitude' => 'float',
    'address.longitude' => 'float',
    'source' => ProspectDataSource::class,
];

// Filter values are automatically cast
?age=25          // Cast to integer
?birth_date=1990-01-01  // Cast to Carbon date
?height=175.5    // Cast to float
?source=erp      // Cast to ProspectDataSource enum
```

## Security Features

- **Model Validation**: Only models that exist and implement the required methods are accessible
- **Attribute Validation**: Only fields defined in `getFilterableAttributes()` are processed
- **Type Safety**: Automatic casting prevents type-related issues
- **Authentication**: All endpoints require authentication via Laravel Sanctum
- **Authorization**: Uses Laravel's Gate system for model-level authorization
- **Input Sanitization**: Query parameters are properly handled and validated

## Error Handling

- **404 Not Found**: When model doesn't exist or isn't filterable
- **403 Forbidden**: When user doesn't have permission to access the model
- **400 Bad Request**: When filter syntax is invalid
- **500 Internal Server Error**: When casting fails or other internal errors occur

### Error Response Examples

```json
{
  "message": "Model not found or not filterable"
}
```

## Performance Considerations

- **Indexing**: Ensure filterable fields are properly indexed in MongoDB
- **Pagination**: Results are automatically paginated to prevent large result sets
- **Query Optimization**: The system uses Laravel's query builder for efficient database queries
- **Resource Collections**: Uses Laravel's Resource Collections for consistent API responses

## Extending the System

### Adding New Filter Types

To add new filter types, extend the `scopeApplyFilters()` method in the `HasFilterable` trait:

```php
switch (true) {
    case Str::startsWith($key, 'min_'):
        $operator = '>=';
        $baseField = Str::replaceStart('min_', '', $key);
        break;
    case Str::startsWith($key, 'max_'):
        $operator = '<=';
        $baseField = Str::replaceStart('max_', '', $key);
        break;
    case Str::startsWith($key, 'like_'):
        $operator = 'like';
        $baseField = Str::replaceStart('like_', '', $key);
        break;
    case Str::endsWith($key, '_not_in'):
        $operator = 'not_in';
        $baseField = Str::replaceEnd('_not_in', '', $key);
        break;
    case Str::endsWith($key, '_in'):
        $operator = 'in';
        $baseField = Str::replaceEnd('_in', '', $key);
        break;
}
```

### Adding New Models

1. Add the `HasFilterable` trait to your model
2. Implement `getFilterableAttributes()` method
3. Add the model to the `resolveModel()` method in `GenericFilterController`
4. Ensure proper casts are defined
5. Create appropriate MongoDB indexes
6. Implement the required policy for authorization

### Example: Adding a Movie Model

```php
<?php

namespace App\Models;

use App\Traits\HasFilterable;
use MongoDB\Laravel\Eloquent\Model;

final class Movie extends Model
{
    use HasFilterable;

    protected $fillable = ['title', 'year', 'runtime', 'imdb', 'plot', 'actors'];

    protected $casts = [
        'year' => 'integer',
        'runtime' => 'integer',
        'imdb' => 'float',
        'created_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * @return array<string, string>
     */
    public static function getFilterableAttributes(): array
    {
        return [
            'year' => 'range',
            'runtime' => 'range',
            'imdb' => 'range',
        ];
    }
}
```

Then add to the controller:

```php
private function resolveModel(string $slug): string
{
    return match ($slug) {
        'prospects' => Prospect::class,
        'movies' => Movie::class,
        default => '',
    };
}
```

## Best Practices

1. **Define Clear Filter Types**: Use appropriate filter types (enum vs range) for your data
2. **Index Filterable Fields**: MongoDB indexes improve query performance significantly
3. **Validate Input**: The system handles basic validation, but consider additional validation for complex scenarios
4. **Document Available Filters**: Provide clear documentation of available filter options for API consumers
5. **Test Thoroughly**: Ensure all filter combinations work as expected
6. **Use Proper Casting**: Define appropriate casts for all filterable fields
7. **Optimize Queries**: Use compound indexes for frequently combined filters
8. **Monitor Performance**: Track query performance and optimize as needed
9. **Implement Policies**: Ensure proper authorization policies are in place for new models

## Troubleshooting

### Common Issues

1. **Filter Not Working**: Check if the field is defined in `getFilterableAttributes()`
2. **Type Casting Errors**: Verify the model's `$casts` property is correctly configured
3. **Dot Notation Issues**: Ensure the field exists in the database and is properly cast
4. **Performance Issues**: Check MongoDB indexes on filterable fields
5. **Authentication Errors**: Ensure proper Sanctum token is provided
6. **Authorization Errors**: Check if the user has permission to access the model

### Debugging

Enable Laravel's query logging to see the generated MongoDB queries:

```php
DB::enableQueryLog();
// ... perform filter operation
dd(DB::getQueryLog());
```

### Common Debugging Scenarios

```php
// Check if model is filterable
$modelClass = Prospect::class;
$isFilterable = method_exists($modelClass, 'scopeApplyFilters');
dd($isFilterable); // Should return true

// Check available filterable attributes
$attributes = Prospect::getFilterableAttributes();
dd($attributes);

// Check search criteria
$criteria = Prospect::searchCriteria();
dd($criteria);
```

## API Testing

### Using Postman

The system includes a Postman collection (`docs/APDE.postman_collection.json`) with examples for testing the GenericFilter endpoints.

### Example cURL Commands

```bash
# Get search criteria
curl -X GET "http://localhost:8000/api/prospects/search-criteria" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# Filter prospects
curl -X GET "http://localhost:8000/api/prospects/filter?gender=male&min_age=25" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# Complex filtering with pagination
curl -X GET "http://localhost:8000/api/prospects/filter?source=erp&min_age=30&max_age=50&page=1&per_page=20" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## Version Compatibility

- **Laravel**: 10.x or higher
- **MongoDB**: 4.4 or higher
- **PHP**: 8.1 or higher
- **Package**: `mongodb/laravel`
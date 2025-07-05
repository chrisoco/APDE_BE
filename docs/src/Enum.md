# Enum System Documentation

## Overview

The application uses a standardized enum system with the `HasEnumHelpers` trait to ensure consistency across all enums. This system provides common functionality while enforcing proper implementation patterns.

## HasEnumHelpers Trait

The `HasEnumHelpers` trait provides common enum functionality and enforces implementation of required methods.

### Location
```
app/Traits/HasEnumHelpers.php
```

### Methods

#### `label(): string`
- **Purpose**: Returns a human-readable label for the enum case
- **Implementation**: Provided by the trait with automatic title case conversion
- **Override**: Can be overridden in individual enums for custom labels
- **Example**: `UserRole::ADMIN->label()` returns `"Admin"`
- **Auto-generation**: `SUPER_ADMIN` → `"Super Admin"`, `DRAFT` → `"Draft"`

#### `values(): array<int, string>` (Static)
- **Purpose**: Returns all enum values as an array
- **Implementation**: Provided by the trait
- **Example**: `UserRole::values()` returns `['guest', 'user', 'admin', 'super_admin']`

#### `labels(): array<int, string>` (Static)
- **Purpose**: Returns all enum labels as an array
- **Implementation**: Provided by the trait
- **Example**: `UserRole::labels()` returns `['Guest', 'User', 'Admin', 'Super Admin']`

## Existing Enums

### UserRole
**Location**: `app/Enums/UserRole.php`

**Cases**:
- `GUEST` → `'guest'` → `'Guest'`
- `USER` → `'user'` → `'User'`
- `ADMIN` → `'admin'` → `'Admin'`
- `SUPER_ADMIN` → `'super_admin'` → `'Super Admin'`

**Usage**:
```php
// Get a specific role
$role = UserRole::ADMIN;

// Get the label
echo $role->label(); // "Admin"

// Get all values
$values = UserRole::values(); // ['guest', 'user', 'admin', 'super_admin']

// Get all labels
$labels = UserRole::labels(); // ['Guest', 'User', 'Admin', 'Super Admin']
```

### CampaignStatus
**Location**: `app/Enums/CampaignStatus.php`

**Cases**:
- `DRAFT` → `'draft'` → `'Draft'`
- `ACTIVE` → `'active'` → `'Active'`
- `PAUSED` → `'paused'` → `'Paused'`
- `COMPLETED` → `'completed'` → `'Completed'`

**Usage**:
```php
// Check campaign status
$status = CampaignStatus::ACTIVE;

// Get the label
echo $status->label(); // "Active"

// Get all possible statuses
$allStatuses = CampaignStatus::values(); // ['draft', 'active', 'paused', 'completed']
```

### ProspectDataSource
**Location**: `app/Enums/ProspectDataSource.php`

**Cases**:
- `ERP` → `'erp'` → `'ERP'`
- `KUEBA` → `'kueba'` → `'Küba'`

**Additional Methods**:
- `importAction(): string` - Returns the import action class for the data source

**Usage**:
```php
// Get data source
$source = ProspectDataSource::ERP;

// Get the label
echo $source->label(); // "ERP"

// Get import action class
$actionClass = $source->importAction(); // "App\Actions\Import\ImportErpProspects"
```

## Creating New Enums

### Basic Template (Automatic Labels)
```php
<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum YourNewEnum: string
{
    use HasEnumHelpers;

    case CASE_ONE = 'case_one';
    case CASE_TWO = 'case_two';
    case CASE_THREE = 'case_three';
    
    // No label() method needed - automatic title case conversion
    // CASE_ONE → "Case One"
    // CASE_TWO → "Case Two" 
    // CASE_THREE → "Case Three"
}
```

### With Custom Labels (Override)
```php
<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum YourNewEnum: string
{
    use HasEnumHelpers;

    case CASE_ONE = 'case_one';
    case CASE_TWO = 'case_two';

    // Override for custom labels
    public function label(): string
    {
        return match ($this) {
            self::CASE_ONE => 'Custom Label One',
            self::CASE_TWO => 'Custom Label Two',
        };
    }

    // Additional enum-specific methods
    public function isActive(): bool
    {
        return match ($this) {
            self::CASE_ONE => true,
            self::CASE_TWO => false,
        };
    }
}
```

## Best Practices

### 1. Naming Conventions
- Use `SCREAMING_SNAKE_CASE` for enum cases
- Use `snake_case` for enum values
- Use `Title Case` for labels

### 2. When to Override label()
- **Keep default**: When automatic title case conversion works perfectly
  - `DRAFT` → `"Draft"`, `ACTIVE` → `"Active"`
- **Override**: When you need custom labels
  - `ERP` → `"ERP"` (not `"Erp"`)
  - `KUEBA` → `"Küba"` (not `"Kueba"`)
  - `SUPER_ADMIN` → `"Super Admin"` (works automatically)

### 3. Implementation Requirements
- Always use the `HasEnumHelpers` trait
- Implement the `label()` method only when custom labels are needed
- Use `match` expressions for custom label implementation
- Include all cases in the match expression when overriding

### 4. Type Safety
- Always declare `strict_types=1`
- Use proper return type hints
- Use proper parameter type hints

### 5. Documentation
- Add PHPDoc comments for additional methods
- Document any enum-specific behavior
- Include usage examples in comments

## Common Use Cases

### Form Select Options
```php
// In a form controller
$userRoles = collect(UserRole::cases())->mapWithKeys(function ($role) {
    return [$role->value => $role->label()];
})->toArray();
// Result: ['guest' => 'Guest', 'user' => 'User', 'admin' => 'Admin', 'super_admin' => 'Super Admin']
```

### Validation Rules
```php
// In a form request
public function rules(): array
{
    return [
        'role' => ['required', 'string', Rule::in(UserRole::values())],
        'status' => ['required', 'string', Rule::in(CampaignStatus::values())],
    ];
}
```

### Database Queries
```php
// In a model scope
public function scopeByStatus($query, CampaignStatus $status)
{
    return $query->where('status', $status->value);
}
```

### API Responses
```php
// In an API resource
public function toArray($request)
{
    return [
        'id' => $this->id,
        'status' => $this->status->value,
        'status_label' => $this->status->label(),
        // ... other fields
    ];
}
```

## Migration Considerations

When adding new enum cases to existing enums:

1. **Database**: Ensure the database column can accept the new value
2. **Validation**: Update any validation rules that reference enum values
3. **Tests**: Add tests for the new enum cases
4. **Documentation**: Update this documentation

## Testing

### Example Test
```php
<?php

namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_values_returns_all_enum_values(): void
    {
        $expected = ['guest', 'user', 'admin', 'super_admin'];
        $this->assertEquals($expected, UserRole::values());
    }

    public function test_labels_returns_all_enum_labels(): void
    {
        $expected = ['Guest', 'User', 'Admin', 'Super Admin'];
        $this->assertEquals($expected, UserRole::labels());
    }

    public function test_label_returns_correct_label_for_each_case(): void
    {
        $this->assertEquals('Guest', UserRole::GUEST->label());
        $this->assertEquals('User', UserRole::USER->label());
        $this->assertEquals('Admin', UserRole::ADMIN->label());
        $this->assertEquals('Super Admin', UserRole::SUPER_ADMIN->label());
    }
}
``` 
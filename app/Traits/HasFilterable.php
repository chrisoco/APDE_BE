<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\Builder;

trait HasFilterable
{
    /**
     * @return array<string, string>
     */
    abstract public static function getFilterableAttributes(): array;

    /**
     * @return array<string, mixed>
     */
    public static function searchCriteria(): array
    {
        $searchCriteria = [];
        $filterableAttributes = static::getFilterableAttributes();

        $collection = static::select(
            ...collect(array_keys($filterableAttributes))->map(
                fn (string $field): string => explode('.', $field)[0]
            )
                ->unique()
                ->values()
                ->toArray()
        )->get();

        foreach ($filterableAttributes as $field => $type) {
            $searchCriteria[$field] = match ($type) {
                'enum' => $collection
                    ->pluck($field)
                    ->unique()
                    ->filter()
                    ->values()
                    ->toArray(),
                'range' => [
                    'min' => $collection->min($field),
                    'max' => $collection->max($field),
                ],
                default => [],
            };
        }

        return $searchCriteria;
    }

    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $filters
     */
    public function scopeApplyFilters(Builder $query, array $filters): void
    {
        $filterableAttributes = static::getFilterableAttributes();

        foreach ($filters as $key => $value) {

            $operator = '=';
            $baseField = $key;

            switch (true) {
                case Str::startsWith($key, 'min_'):
                    $operator = '>=';
                    $baseField = Str::replaceStart('min_', '', $key);
                    break;
                case Str::startsWith($key, 'max_'):
                    $operator = '<=';
                    $baseField = Str::replaceStart('max_', '', $key);
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

            // Note: When receiving query parameters, PHP automatically replaces dots (.) in parameter names with underscores (_).
            // For example, a query param like ?address.city=London will be available as $_GET['address_city'].
            // To support filterable attributes defined with dot notation (e.g., 'address.city'), we check if the base field
            // with underscores replaced by dots exists in the filterable attributes array.
            // This does not allow mixing of _ and . notation in a key; the key must be either all underscores or all dots to match.
            $dotField = str_replace('_', '.', $baseField);
            if (array_key_exists($dotField, $filterableAttributes)) {
                $baseField = $dotField;
            }

            // Skip this filter if the base field is not defined as filterable in the model.
            if (! array_key_exists($baseField, $filterableAttributes)) {
                continue;
            }

            // Skip range operators (>=, <=) for enum filterable attributes, as it doesn't make sense to apply range filters to enums.
            if ($filterableAttributes[$baseField] === 'enum' && ($operator === '>=' || $operator === '<=')) {
                continue;
            }

            $query = match ($operator) {
                'in' => $query->whereIn(
                    $baseField,
                    array_map(fn ($v) => $this->castFilterValue($baseField, $v), (array) $value)
                ),
                'not_in' => $query->whereNotIn(
                    $baseField,
                    array_map(fn ($v) => $this->castFilterValue($baseField, $v), (array) $value)
                ),
                '=', '>=', '<=' => $query->where(
                    $baseField,
                    $operator,
                    $this->castFilterValue($baseField, $value)
                ),
            };

        }

    }

    private function castFilterValue(string $field, mixed $value): mixed
    {
        $cast = $this->casts[$field] ?? 'string';

        return match ($cast) {
            'integer', 'int' => is_numeric($value) ? (int) $value : $value,
            'float', 'double' => is_numeric($value) ? (float) $value : $value,
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'date', 'datetime' => is_string($value) || is_numeric($value) ? Carbon::parse($value) : $value,
            default => $cast !== 'string' && enum_exists($cast) && method_exists($cast, 'from')
                ? $cast::from($value)
                : $value,
        };
    }
}

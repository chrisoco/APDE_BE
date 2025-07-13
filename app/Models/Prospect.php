<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProspectDataSource;
use App\Policies\ProspectPolicy;
use App\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
#[UsePolicy(ProspectPolicy::class)]
final class Prospect extends Model
{
    use AuthorizesRequests, HasFilterable, SoftDeletes;

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
        // 'address' => 'array', !Laravel Serializes to JSON string and therefor breaks dot "." notation.
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

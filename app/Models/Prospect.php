<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DataSource;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $external_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $maiden_name
 * @property int|null $age
 * @property string|null $gender
 * @property string $email
 * @property string|null $phone
 * @property string|null $username
 * @property \Carbon\Carbon|null $birth_date
 * @property string|null $image
 * @property string|null $blood_group
 * @property float|null $height
 * @property float|null $weight
 * @property string|null $eye_color
 * @property string|null $hair_color
 * @property string|null $hair_type
 * @property array<string, mixed>|null $address
 * @property string|null $university
 * @property array<string, mixed>|null $bank
 * @property array<string, mixed>|null $company
 * @property string|null $ein
 * @property string|null $ssn
 * @property string|null $role
 * @property DataSource|null $source
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
final class Prospect extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'external_id',
        'first_name',
        'last_name',
        'maiden_name',
        'age',
        'gender',
        'email',
        'phone',
        'username',
        'birth_date',
        'image',
        'blood_group',
        'height',
        'weight',
        'eye_color',
        'hair_color',
        'hair_type',
        'address', // JSON
        'university',
        'bank', // JSON
        'company', // JSON
        'ein',
        'ssn',
        'role',
        'source',
    ];

    protected $casts = [
        'age' => 'integer',
        'birth_date' => 'date',
        'height' => 'float',
        'weight' => 'float',
        'address' => 'array',
        'bank' => 'array',
        'company' => 'array',
        'crypto' => 'array',
        'source' => DataSource::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

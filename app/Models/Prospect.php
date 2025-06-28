<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProspectDataSource;
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
    use SoftDeletes;

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
        'address' => 'array',
        'source' => ProspectDataSource::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $title
 * @property string $description
 * @property CampaignStatus $status
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property array<string, mixed>|null $prospect_filter
 *
 * @template TFactory of \Illuminate\Database\Eloquent\Factories\Factory<static>
 */
final class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'title', 'description', 'status', 'start_date', 'end_date', 'prospect_filter', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'status' => CampaignStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

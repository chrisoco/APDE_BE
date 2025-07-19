<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Policies\CampaignPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use MongoDB\Laravel\Relations\HasMany;
use MongoDB\Laravel\Relations\HasOne;

/**
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property CampaignStatus $status
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property array<string, mixed>|null $prospect_filter
 */
#[UsePolicy(CampaignPolicy::class)]
final class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
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

    /**
     * Get the landingpage associated with the campaign.
     *
     * @return HasOne<Landingpage, Campaign>
     */
    public function landingpage(): HasOne
    {
        return $this->hasOne(Landingpage::class);
    }

    /**
     * Get the trackings for the campaign.
     *
     * @return HasMany<CampaignTracking, Campaign>
     */
    public function trackings(): HasMany
    {
        return $this->hasMany(CampaignTracking::class);
    }

    /**
     * Get the prospects associated with the campaign.
     *
     * @return HasMany<CampainProspect, Campaign>
     */
    public function campaignProspects(): HasMany
    {
        return $this->hasMany(CampainProspect::class);
    }
}

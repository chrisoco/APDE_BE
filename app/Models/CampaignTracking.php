<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @property string $id
 * @property string|null $campaign_id
 * @property string|null $landingpage_id
 * @property string|null $prospect_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referrer
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_content
 * @property string|null $utm_term
 * @property string|null $gclid
 * @property string|null $fbclid
 * @property array<string, mixed> $tracking_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class CampaignTracking extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignTrackingFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'campaign_id',
        'landingpage_id',
        'prospect_id',
        'ip_address',
        'user_agent',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'gclid',
        'fbclid',
        'tracking_data',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the campaign associated with this tracking record.
     *
     * @return BelongsTo<Campaign, CampaignTracking>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the landing page associated with this tracking record.
     *
     * @return BelongsTo<Landingpage, CampaignTracking>
     */
    public function landingpage(): BelongsTo
    {
        return $this->belongsTo(Landingpage::class);
    }

    /**
     * Get the prospect associated with this tracking record.
     *
     * @return BelongsTo<Prospect, CampaignTracking>
     */
    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    /**
     * Get UTM parameters as array.
     *
     * @return array<string, string|null>
     */
    public function getUtmParameters(): array
    {
        return array_filter([
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_content' => $this->utm_content,
            'utm_term' => $this->utm_term,
        ]);
    }

    /**
     * Scope to filter by campaign.
     *
     * @param  Builder<CampaignTracking>  $query
     */
    #[Scope]
    private function forCampaign(Builder $query, string $campaignId): void
    {
        $query->where('campaign_id', $campaignId);
    }

    /**
     * Scope to filter by date range.
     *
     * @param  Builder<CampaignTracking>  $query
     * @param  mixed  $startDate
     * @param  mixed  $endDate
     */
    #[Scope]
    private function dateRange(Builder $query, $startDate, $endDate): void
    {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

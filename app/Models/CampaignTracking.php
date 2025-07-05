<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string|null $campaign_id
 * @property string|null $landingpage_id
 * @property string|null $prospect_id
 * @property string $session_id
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
 * @property \Illuminate\Support\Carbon $first_visit_at
 * @property \Illuminate\Support\Carbon|null $last_visit_at
 * @property int $visit_count
 * @property bool $converted
 * @property \Illuminate\Support\Carbon|null $converted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
final class CampaignTracking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'campaign_id',
        'landingpage_id',
        'prospect_id',
        'session_id',
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
        'first_visit_at',
        'last_visit_at',
        'visit_count',
        'converted',
        'converted_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'tracking_data' => 'array',
        'first_visit_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'converted_at' => 'datetime',
        'visit_count' => 'integer',
        'converted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the campaign associated with this tracking record.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the landing page associated with this tracking record.
     */
    public function landingpage(): BelongsTo
    {
        return $this->belongsTo(Landingpage::class);
    }

    /**
     * Get the prospect associated with this tracking record.
     */
    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    /**
     * Get UTM parameters as array.
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
     * Mark this tracking record as converted.
     */
    public function markAsConverted(): void
    {
        $this->update([
            'converted' => true,
            'converted_at' => now(),
        ]);
    }

    /**
     * Increment visit count and update last visit.
     */
    public function recordVisit(): void
    {
        $this->update([
            'visit_count' => $this->visit_count + 1,
            'last_visit_at' => now(),
        ]);
    }

    /**
     * Scope to filter by campaign.
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    private function forCampaign($query, string $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    /**
     * Scope to filter by conversion status.
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    private function converted($query)
    {
        return $query->where('converted', true);
    }

    /**
     * Scope to filter by date range.
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    private function dateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('first_visit_at', [$startDate, $endDate]);
    }
}

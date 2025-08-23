<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Campaign;
use App\Models\Prospect;
use Illuminate\Support\Collection;

final class CampaignAnalyticsService
{
    /**
     * Get the analytics data for the specified campaign.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAnalyticsData(Campaign $campaign): array
    {
        return [
            'campaign_overview' => [
                'campaign_id' => $campaign->id,
                'campaign_title' => $campaign->title,
                'status' => $campaign->status->value,
                'start_date' => $campaign->start_date?->toISOString(),
                'end_date' => $campaign->end_date?->toISOString(),
            ],
            'visits' => $this->getVisits($campaign),
            'statistics' => $this->getStatistics($campaign),
            'device_browser_breakdown' => $this->getDeviceBrowserBreakdown($campaign),
            'utm_sources' => $this->getUtmSourceBreakdown($campaign),
        ];
    }

    /**
     * Get email statistics for the specified campaign.
     *
     * @return array<string, int>
     */
    public function getEmailStatistics(Campaign $campaign): array
    {
        if (! $campaign->prospect_filter) {
            return [
                'campaign' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                ],
                'total_emails_sent' => 0,
                'notified_prospects' => 0,
                'available_prospects' => 0,
                'total_prospects' => 0,
            ];
        }

        // Filter prospects based on campaign filters
        $filteredProspects = Prospect::applyFilters($campaign->prospect_filter);
        $totalProspects = $filteredProspects->count();
        $notifiedUniqueProspects = $campaign->campaignProspects()->pluck('prospect_id')->unique()->count();

        return [
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
            ],
            'total_emails_sent' => $campaign->campaignProspects()->count(),
            'notified_prospects' => $notifiedUniqueProspects,
            'available_prospects' => $totalProspects - $notifiedUniqueProspects,
            'total_prospects' => $totalProspects,
        ];
    }

    /**
     * Get the visits for the specified campaign.
     *
     * @return array<string, int>
     */
    private function getVisits(Campaign $campaign): array
    {
        $totalVisits = $campaign->trackings()->count();
        $totalUniqueVisitsIP = $campaign->trackings()->distinct('ip_address')->count();
        $totalUniqueVisits = $campaign->trackings()
            ->whereNotNull('ip_address')
            ->get(['ip_address', 'user_agent'])
            ->map(fn ($tracking): string => $tracking->ip_address.'|'.($tracking->user_agent ?? 'no_user_agent'))
            ->unique()
            ->count();

        return [
            'total' => $totalVisits,
            'unique_ip' => $totalUniqueVisitsIP,
            'total_unique' => $totalUniqueVisits,
        ];
    }

    /**
     * Get the statistics for the specified campaign.
     *
     * @return array<string, float|int>
     */
    private function getStatistics(Campaign $campaign): array
    {
        $totalProspectsNotified = $campaign->campaignProspects()->pluck('prospect_id')->unique()->count();
        $uniqueProspectVisits = $campaign->trackings()->whereNotNull('prospect_id')->distinct('prospect_id')->count();
        $emailCtaClickRate = $uniqueProspectVisits > 0 && $totalProspectsNotified > 0 ? round(($uniqueProspectVisits / $totalProspectsNotified) * 100, 2) : 0;

        return [
            'total_prospects_notified' => $totalProspectsNotified,
            'unique_prospect_visits' => $uniqueProspectVisits,
            'email_cta_click_rate' => $emailCtaClickRate,
        ];
    }

    /**
     * Get the device browser breakdown for the specified campaign.
     *
     * @return array<string, array<mixed>>
     */
    private function getDeviceBrowserBreakdown(Campaign $campaign): array
    {
        $trackingData = $campaign->trackings()
            ->whereNotNull('tracking_data')
            ->pluck('tracking_data');

        return [
            'device_types' => $this->createBreakdown($trackingData, 'device_type'),
            'browsers' => $this->createBreakdown($trackingData, 'browser'),
            'operating_systems' => $this->createBreakdown($trackingData, 'os'),
            'languages' => $this->createBreakdown($trackingData, 'language'),
        ];
    }

    /**
     * Create a breakdown of the tracking data.
     *
     * @param  Collection<int|string, mixed>  $trackingData
     * @return array<string, int>
     */
    private function createBreakdown(Collection $trackingData, string $key): array
    {
        /* @phpstan-ignore-next-line */
        return $trackingData->groupBy(fn ($data): string => $data[$key] ?? 'unknown')->map(fn ($group): int => $group->count())->toArray();
    }

    /**
     * Get the UTM source breakdown for the specified campaign.
     *
     * @return array<string, array<mixed>>
     */
    private function getUtmSourceBreakdown(Campaign $campaign): array
    {
        $tracking = $campaign->trackings()
            ->whereNotNull('utm_source')
            ->get();

        return [
            'source' => $tracking->groupBy('utm_source')->map(fn (mixed $group): int => $group->count())->toArray(),
            'medium' => $tracking->groupBy('utm_medium')->map(fn (mixed $group): int => $group->count())->toArray(),
        ];
    }
}

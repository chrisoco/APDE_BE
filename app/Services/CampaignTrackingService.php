<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignTracking;
use App\Models\Landingpage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class CampaignTrackingService
{
    /**
     * Track a new visit with UTM parameters.
     */
    public function trackVisit(Request $request, ?string $campaignId = null, ?string $landingpageId = null): CampaignTracking
    {
        $sessionId = $request->session()->getId();

        // Check if we already have a tracking record for this session
        $tracking = CampaignTracking::where('session_id', $sessionId)->first();

        if ($tracking) {
            // Update existing tracking record
            $tracking->recordVisit();

            // Update campaign/landingpage if provided
            if ($campaignId !== null && $campaignId !== '' && $campaignId !== '0') {
                $tracking->update(['campaign_id' => $campaignId]);
            }
            if ($landingpageId !== null && $landingpageId !== '' && $landingpageId !== '0') {
                $tracking->update(['landingpage_id' => $landingpageId]);
            }

            return $tracking;
        }

        // Create new tracking record
        return CampaignTracking::create([
            'campaign_id' => $campaignId,
            'landingpage_id' => $landingpageId,
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
            'utm_content' => $request->get('utm_content'),
            'utm_term' => $request->get('utm_term'),
            'gclid' => $request->get('gclid'),
            'fbclid' => $request->get('fbclid'),
            'tracking_data' => $this->extractTrackingData($request),
            'first_visit_at' => now(),
            'last_visit_at' => now(),
            'visit_count' => 1,
            'converted' => false,
        ]);
    }

    /**
     * Track a conversion (e.g., form submission, purchase).
     */
    public function trackConversion(string $sessionId, ?string $prospectId = null): ?CampaignTracking
    {
        $tracking = CampaignTracking::where('session_id', $sessionId)->first();

        if ($tracking) {
            $tracking->markAsConverted();

            if ($prospectId !== null && $prospectId !== '' && $prospectId !== '0') {
                $tracking->update(['prospect_id' => $prospectId]);
            }

            return $tracking;
        }

        return null;
    }

    /**
     * Generate UTM tracking URL for a campaign.
     */
    public function generateTrackingUrl(Campaign $campaign, Landingpage $landingpage, array $utmParams = []): string
    {
        $baseUrl = config('app.url').'/landing/'.$landingpage->slug;

        $defaultParams = [
            'utm_source' => $utmParams['utm_source'] ?? 'direct',
            'utm_medium' => $utmParams['utm_medium'] ?? 'web',
            'utm_campaign' => $utmParams['utm_campaign'] ?? Str::slug($campaign->title),
            'utm_content' => $utmParams['utm_content'] ?? null,
            'utm_term' => $utmParams['utm_term'] ?? null,
        ];

        // Filter out null values
        $params = array_filter($defaultParams, fn ($value): bool => $value !== null);

        return $baseUrl.'?'.http_build_query($params);
    }

    /**
     * Get campaign analytics.
     */
    public function getCampaignAnalytics(string $campaignId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = CampaignTracking::forCampaign($campaignId);

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $totalVisits = $query->count();
        $uniqueVisitors = $query->distinct('session_id')->count();
        $conversions = $query->converted()->count();
        $conversionRate = $totalVisits > 0 ? ($conversions / $totalVisits) * 100 : 0;

        // UTM source breakdown
        $utmSourceBreakdown = $query->selectRaw('utm_source, COUNT(*) as count')
            ->groupBy('utm_source')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'utm_source')
            ->toArray();

        // Daily visits
        $dailyVisits = $query->selectRaw('DATE(first_visit_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        return [
            'total_visits' => $totalVisits,
            'unique_visitors' => $uniqueVisitors,
            'conversions' => $conversions,
            'conversion_rate' => round($conversionRate, 2),
            'utm_source_breakdown' => $utmSourceBreakdown,
            'daily_visits' => $dailyVisits,
        ];
    }

    /**
     * Extract additional tracking data from request.
     */
    private function extractTrackingData(Request $request): array
    {
        return [
            'language' => $request->getPreferredLanguage(),
            'timezone' => $request->header('timezone'),
            'screen_resolution' => $request->get('screen_resolution'),
            'device_type' => $this->detectDeviceType($request->userAgent()),
            'browser' => $this->detectBrowser($request->userAgent()),
            'os' => $this->detectOS($request->userAgent()),
        ];
    }

    /**
     * Detect device type from user agent.
     */
    private function detectDeviceType(?string $userAgent): ?string
    {
        if ($userAgent === null || $userAgent === '' || $userAgent === '0') {
            return null;
        }

        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/Tablet|iPad/', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Detect browser from user agent.
     */
    private function detectBrowser(?string $userAgent): ?string
    {
        if ($userAgent === null || $userAgent === '' || $userAgent === '0') {
            return null;
        }

        if (preg_match('/Chrome/', $userAgent)) {
            return 'Chrome';
        }

        if (preg_match('/Firefox/', $userAgent)) {
            return 'Firefox';
        }

        if (preg_match('/Safari/', $userAgent)) {
            return 'Safari';
        }

        if (preg_match('/Edge/', $userAgent)) {
            return 'Edge';
        }

        return 'Other';
    }

    /**
     * Detect OS from user agent.
     */
    private function detectOS(?string $userAgent): ?string
    {
        if ($userAgent === null || $userAgent === '' || $userAgent === '0') {
            return null;
        }

        if (preg_match('/Windows/', $userAgent)) {
            return 'Windows';
        }

        if (preg_match('/Mac/', $userAgent)) {
            return 'macOS';
        }

        if (preg_match('/Linux/', $userAgent)) {
            return 'Linux';
        }

        if (preg_match('/Android/', $userAgent)) {
            return 'Android';
        }

        if (preg_match('/iOS/', $userAgent)) {
            return 'iOS';
        }

        return 'Other';
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CampaignTracking;
use App\Models\Landingpage;
use Illuminate\Http\Request;

final class CampaignTrackingService
{
    /**
     * Track a new visit with UTM parameters.
     */
    public function trackLandingPageVisit(Request $request, Landingpage $landingpage): CampaignTracking
    {
        // TODO: Validate Signed URL: create RequestValidator?
        // if($request->has('prospect') && ! $request->hasValidSignature()) {
        //     return null;
        // }

        // Create new tracking record
        return CampaignTracking::create([
            'campaign_id' => $landingpage->campaign_id,
            'landingpage_id' => $landingpage->id,
            'prospect_id' => $request->get('prospect'),
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
        ]);
    }

    /**
     * Generate UTM tracking URL for a campaign.
     */
    // public function generateTrackingUrl(Campaign $campaign, Landingpage $landingpage, array $utmParams = []): string
    // {
    //     $baseUrl = config('app.url').'/landing/'.$landingpage->slug;

    //     $defaultParams = [
    //         'utm_source' => $utmParams['utm_source'] ?? 'direct',
    //         'utm_medium' => $utmParams['utm_medium'] ?? 'web',
    //         'utm_campaign' => $utmParams['utm_campaign'] ?? Str::slug($campaign->title),
    //         'utm_content' => $utmParams['utm_content'] ?? null,
    //         'utm_term' => $utmParams['utm_term'] ?? null,
    //     ];

    //     // Filter out null values
    //     $params = array_filter($defaultParams, fn ($value): bool => $value !== null);

    //     return $baseUrl.'?'.http_build_query($params);
    // }

    /**
     * Get campaign analytics.
     *
     * @return array<string, mixed>
     */
    public function getCampaignAnalytics(string $campaignId): array
    {
        CampaignTracking::forCampaign($campaignId)->get();

        return [
            //
        ];
    }

    /**
     * Extract additional tracking data from request.
     *
     * @return array<string, mixed>
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

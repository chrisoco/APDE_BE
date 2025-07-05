<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignTracking;
use App\Services\CampaignTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class CampaignTrackingController extends Controller
{
    public function __construct(
        private readonly CampaignTrackingService $trackingService
    ) {}

    /**
     * Track a visit to a landing page.
     */
    public function trackVisit(Request $request, string $landingpageSlug): JsonResponse
    {
        // Find the landing page
        $landingpage = \App\Models\Landingpage::where('slug', $landingpageSlug)->firstOrFail();

        // Track the visit
        $tracking = $this->trackingService->trackVisit(
            $request,
            $landingpage->campaign_id,
            $landingpage->id
        );

        return response()->json([
            'success' => true,
            'tracking_id' => $tracking->id,
            'session_id' => $tracking->session_id,
        ]);
    }

    /**
     * Track a conversion (form submission, etc.).
     */
    public function trackConversion(Request $request): JsonResponse
    {
        $sessionId = $request->session()->getId();
        $prospectId = $request->get('prospect_id');

        $tracking = $this->trackingService->trackConversion($sessionId, $prospectId);

        if ($tracking instanceof CampaignTracking) {
            return response()->json([
                'success' => true,
                'converted' => true,
                'converted_at' => $tracking->converted_at,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No tracking session found',
        ], 404);
    }

    /**
     * Get analytics for a specific campaign.
     */
    public function getCampaignAnalytics(Request $request, Campaign $campaign): JsonResponse
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $analytics = $this->trackingService->getCampaignAnalytics(
            $campaign->id,
            $startDate,
            $endDate
        );

        return response()->json([
            'campaign_id' => $campaign->id,
            'campaign_title' => $campaign->title,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get tracking data for a campaign.
     */
    public function getTrackingData(Campaign $campaign): ResourceCollection
    {
        return CampaignTracking::forCampaign($campaign->id)
            ->with(['landingpage', 'prospect'])
            ->orderByDesc('first_visit_at')
            ->paginate(20)
            ->toResourceCollection();
    }

    /**
     * Generate tracking URL for a campaign.
     */
    public function generateTrackingUrl(Request $request, Campaign $campaign): JsonResponse
    {
        $request->validate([
            'landingpage_id' => 'required|exists:landingpages,id',
            'utm_source' => 'sometimes|string|max:255',
            'utm_medium' => 'sometimes|string|max:255',
            'utm_campaign' => 'sometimes|string|max:255',
            'utm_content' => 'sometimes|string|max:255',
            'utm_term' => 'sometimes|string|max:255',
        ]);

        $landingpage = \App\Models\Landingpage::findOrFail($request->landingpage_id);

        $utmParams = $request->only([
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'utm_term',
        ]);

        $trackingUrl = $this->trackingService->generateTrackingUrl($campaign, $landingpage, $utmParams);

        return response()->json([
            'campaign_id' => $campaign->id,
            'landingpage_id' => $landingpage->id,
            'tracking_url' => $trackingUrl,
            'utm_parameters' => array_filter($utmParams),
        ]);
    }

    /**
     * Get overall tracking statistics.
     */
    public function getOverallStats(): JsonResponse
    {
        $totalVisits = CampaignTracking::count();
        $uniqueVisitors = CampaignTracking::distinct('session_id')->count();
        $totalConversions = CampaignTracking::converted()->count();
        $overallConversionRate = $totalVisits > 0 ? ($totalConversions / $totalVisits) * 100 : 0;

        // Top campaigns by visits
        $topCampaigns = CampaignTracking::selectRaw('campaign_id, COUNT(*) as visit_count')
            ->groupBy('campaign_id')
            ->orderByDesc('visit_count')
            ->limit(5)
            ->get()
            ->map(function ($item): array {
                $campaign = Campaign::find($item->campaign_id);

                return [
                    'campaign_id' => $item->campaign_id,
                    'campaign_title' => $campaign?->title ?? 'Unknown',
                    'visit_count' => $item->visit_count,
                ];
            });

        // Top UTM sources
        $topUtmSources = CampaignTracking::selectRaw('utm_source, COUNT(*) as count')
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->pluck('count', 'utm_source')
            ->toArray();

        return response()->json([
            'total_visits' => $totalVisits,
            'unique_visitors' => $uniqueVisitors,
            'total_conversions' => $totalConversions,
            'overall_conversion_rate' => round($overallConversionRate, 2),
            'top_campaigns' => $topCampaigns,
            'top_utm_sources' => $topUtmSources,
        ]);
    }
}

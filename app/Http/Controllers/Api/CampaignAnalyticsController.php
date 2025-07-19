<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\CampaignAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

final class CampaignAnalyticsController extends Controller
{
    public function __construct(
        private readonly CampaignAnalyticsService $campaignAnalyticsService
    ) {}

    /**
     * Get analytics for the specified campaign.
     */
    public function show(Campaign $campaign): JsonResponse
    {
        Gate::authorize('viewAnalytics', $campaign);

        $analyticsData = $this->campaignAnalyticsService->getAnalyticsData($campaign);

        return response()->json($analyticsData);
    }
}

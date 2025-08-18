<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CampaignRequest;
use App\Models\Campaign;
use App\Services\CampaignTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;

final class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        Gate::authorize('viewAny', Campaign::class);

        return Campaign::with('landingpage')->paginate(request()->integer('per_page', 10))->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignRequest $request): JsonResource
    {
        Gate::authorize('create', Campaign::class);

        return Campaign::create($request->validated())->load('landingpage')->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign): JsonResource
    {
        Gate::authorize('view', $campaign);

        return $campaign->load('landingpage')->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampaignRequest $request, Campaign $campaign): JsonResource
    {
        Gate::authorize('update', $campaign);

        $campaign->update($request->validated());

        return $campaign->load('landingpage')->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign): JsonResponse
    {
        Gate::authorize('delete', $campaign);

        $campaign->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Display the landingpage for a campaign by identifier.
     *
     * @param  string  $identifier  The campaign identifier (can be uuid or landingpage slug)
     */
    public function showLandingpage(Request $request, CampaignTrackingService $campaignTrackingService, $identifier): JsonResource
    {
        // Try to find campaign by ID first
        if ($campaign = Campaign::with('landingpage')->find($identifier)) {
            // Check if campaign is active and within date range
            abort_if($campaign->status !== CampaignStatus::ACTIVE ||
                ($campaign->start_date && $campaign->start_date > now()) ||
                ($campaign->end_date && $campaign->end_date < now()),
                404
            );
        } else {
            // If not found by ID, try to find active campaign by campaign slug
            $campaign = Campaign::with('landingpage')
                ->where('slug', $identifier)
                ->where('status', CampaignStatus::ACTIVE)
                ->where(function ($q): void {
                    $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($q): void {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->firstOrFail();
        }

        abort_if($campaign->landingpage === null, 404, 'Campaign does not have an associated landing page.');

        $campaignTrackingService->trackLandingPageVisit($request, $campaign);

        return $campaign->load('landingpage')->toResource();
    }
}

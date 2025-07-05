<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return Campaign::with('landingpage')->paginate(10)->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignRequest $request): JsonResource
    {
        return Campaign::create($request->validated())->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign): JsonResource
    {
        return $campaign->load('landingpage')->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampaignRequest $request, Campaign $campaign): JsonResource
    {
        $campaign->update($request->validated());

        return $campaign->load('landingpage')->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign): JsonResource
    {
        $campaign->delete();

        return $campaign->load('landingpage')->toResource();
    }
}

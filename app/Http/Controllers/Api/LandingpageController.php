<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LandingpageRequest;
use App\Models\Landingpage;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;

final class LandingpageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        Gate::authorize('viewAny', Landingpage::class);

        return Landingpage::with('campaign')->paginate(request()->integer('per_page', 10))->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LandingpageRequest $request): JsonResource
    {
        Gate::authorize('create', Landingpage::class);

        return Landingpage::create($request->validated())->toResource();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $identifier  The landingpage identifier (can be uuid or slug)
     */
    public function show($identifier): JsonResource
    {
        if ($landingpage = Landingpage::find($identifier)) {
            Gate::authorize('view', $landingpage);
        } else {

            $landingpage = Landingpage::with('campaign')
                ->where('slug', $identifier)
                ->whereHas('campaign', function ($query): void {
                    $query->where('status', CampaignStatus::ACTIVE)
                        ->where(function ($q): void {
                            $q->whereNull('start_date')
                                ->orWhere('start_date', '<=', now());
                        })
                        ->where(function ($q): void {
                            $q->whereNull('end_date')
                                ->orWhere('end_date', '>=', now());
                        });
                })
                ->firstOrFail();
        }

        return $landingpage->load('campaign')->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LandingpageRequest $request, Landingpage $landingpage): JsonResource
    {
        Gate::authorize('update', $landingpage);

        $landingpage->update($request->validated());

        return $landingpage->load('campaign')->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Landingpage $landingpage): JsonResource
    {
        Gate::authorize('delete', $landingpage);

        $landingpage->delete();

        return $landingpage->load('campaign')->toResource();
    }
}

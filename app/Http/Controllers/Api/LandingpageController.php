<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LandingpageRequest;
use App\Models\Landingpage;
use Illuminate\Http\JsonResponse;
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

        return Landingpage::with('campaigns')->paginate(request()->integer('per_page', 10))->toResourceCollection();
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
     */
    public function show(Landingpage $landingpage): JsonResource
    {
        Gate::authorize('view', $landingpage);

        return $landingpage->load('campaigns')->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LandingpageRequest $request, Landingpage $landingpage): JsonResource
    {
        Gate::authorize('update', $landingpage);

        $landingpage->update($request->validated());

        return $landingpage->load('campaigns')->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Landingpage $landingpage): JsonResponse
    {
        Gate::authorize('delete', $landingpage);

        $landingpage->delete();

        return response()->json(['success' => true]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LandingpageRequest;
use App\Models\Landingpage;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class LandingpageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return Landingpage::with('campaign')->paginate(10)->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LandingpageRequest $request): JsonResource
    {
        return Landingpage::create($request->validated())->toResource();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $identifier  The landingpage identifier (can be uuid or slug)
     */
    public function show($identifier): JsonResource
    {
        if (! $landingpage = Landingpage::find($identifier)) {
            $landingpage = Landingpage::where('slug', $identifier)->firstOrFail();
        }

        return $landingpage->load('campaign')->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LandingpageRequest $request, Landingpage $landingpage): JsonResource
    {
        $landingpage->update($request->validated());

        return $landingpage->load('campaign')->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Landingpage $landingpage): JsonResource
    {
        $landingpage->delete();

        return $landingpage->load('campaign')->toResource();
    }
}

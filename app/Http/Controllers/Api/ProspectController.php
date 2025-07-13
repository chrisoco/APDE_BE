<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;

final class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        Gate::authorize('viewAny', Prospect::class);

        return Prospect::paginate(request()->integer('per_page', 10))->toResourceCollection();
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $prospect): JsonResource
    {
        Gate::authorize('view', $prospect);

        return $prospect->toResource();
    }
}

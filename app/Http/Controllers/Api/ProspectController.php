<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return Prospect::paginate(10)->toResourceCollection();
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $prospect): JsonResource
    {
        return $prospect->toResource();
    }
}

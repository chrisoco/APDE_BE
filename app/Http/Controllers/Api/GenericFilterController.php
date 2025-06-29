<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class GenericFilterController extends Controller
{
    public function filter(Request $request, string $model): ResourceCollection
    {
        $modelClass = $this->resolveModel($model);

        abort_if(! class_exists($modelClass) || ! method_exists($modelClass, 'scopeApplyFilters'), 404, 'Model not found or not filterable');

        $filters = $request->all();

        /** @var ResourceCollection $filtered_collection */
        /** @phpstan-ignore-next-line */
        $filtered_collection = $modelClass::applyFilters($filters)->paginate(10)->toResourceCollection();

        return $filtered_collection;
    }

    public function searchCriteria(Request $request, string $model): JsonResponse
    {
        $modelClass = $this->resolveModel($model);

        abort_if(! class_exists($modelClass) || ! method_exists($modelClass, 'searchCriteria'), 404, 'Model not found or not filterable');

        return response()->json($modelClass::searchCriteria());
    }

    private function resolveModel(string $slug): string
    {
        return match ($slug) {
            'prospects' => Prospect::class,
            // 'campaigns' => \App\Models\Campaign::class,
            default => '',
        };
    }
}

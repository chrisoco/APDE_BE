<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GenericFilterController;
use App\Http\Controllers\Api\ProspectController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/{model}/filter', [GenericFilterController::class, 'filter']);
    Route::get('/{model}/search-criteria', [GenericFilterController::class, 'searchCriteria']);

    Route::apiResource('prospects', ProspectController::class)->only(['index', 'show']);

    Route::get('/movies', function () {
        // Token has ability "view-movies" or global "*"
        return response()->json(App\Models\Movie::all());
    })->middleware(['abilities:view-movies']);
});

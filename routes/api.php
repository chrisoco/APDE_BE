<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CampaignTrackingController;
use App\Http\Controllers\Api\GenericFilterController;
use App\Http\Controllers\Api\LandingpageController;
use App\Http\Controllers\Api\ProspectController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/{model}/filter', [GenericFilterController::class, 'filter']);
    Route::get('/{model}/search-criteria', [GenericFilterController::class, 'searchCriteria']);

    Route::apiResource('prospects', ProspectController::class)->only(['index', 'show']);
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('landingpages', LandingpageController::class);

    // Campaign tracking routes
    Route::prefix('tracking')->group(function () {
        Route::post('/visit/{landingpageSlug}', [CampaignTrackingController::class, 'trackVisit']);
        Route::post('/conversion', [CampaignTrackingController::class, 'trackConversion']);
        Route::get('/stats/overall', [CampaignTrackingController::class, 'getOverallStats']);
    });

    Route::prefix('campaigns/{campaign}')->group(function () {
        Route::get('/analytics', [CampaignTrackingController::class, 'getCampaignAnalytics']);
        Route::get('/tracking-data', [CampaignTrackingController::class, 'getTrackingData']);
        Route::post('/generate-tracking-url', [CampaignTrackingController::class, 'generateTrackingUrl']);
    });

    Route::get('/movies', function () {
        // Token has ability "view-movies" or global "*"
        return response()->json(App\Models\Movie::all());
    })->middleware(['abilities:view-movies']);
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CampaignEmailController;
use App\Http\Controllers\Api\GenericFilterController;
use App\Http\Controllers\Api\LandingpageController;
use App\Http\Controllers\Api\ProspectController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/lp/{identifier}', [LandingpageController::class, 'show'])->name('lp.show');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/{model}/filter', [GenericFilterController::class, 'filter']);
    Route::get('/{model}/search-criteria', [GenericFilterController::class, 'searchCriteria']);

    Route::apiResource('prospects', ProspectController::class)->only(['index', 'show']);
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('landingpages', LandingpageController::class);

    Route::post('/campaigns/{campaign}/send-emails', [CampaignEmailController::class, 'send']);

    Route::get('/cp-cookie', function () {
        // Token has ability "view-cp" or global "*"
        return response()->json(App\Models\Campaign::all());
    })->middleware(['abilities:view-cp']);

});

Route::get('/docs/openapi/', function () {
    return view('docs.openapi.index');
});

Route::get('/docs/openapi/openapi.yaml', function () {
    return response()->file(base_path('docs/openapi/openapi.yaml'), [
        'Content-Type' => 'application/x-yaml',
    ]);
});
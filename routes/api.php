<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/movies', function () {
        // Token has ability "view-movies" or global "*"
        return response()->json(App\Models\Movie::all());
    })->middleware(['abilities:view-movies']);
});

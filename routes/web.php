<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['web', 'auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/movies', function () {
        return App\Models\Movie::all();
    });
});

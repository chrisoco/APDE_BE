<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['web', 'auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/cp-cookie', function () {
        return App\Models\Campaign::all();
    });
});

Route::get('/', function () {
    return response('up');
});

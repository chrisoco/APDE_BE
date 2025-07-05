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

Route::get('/docs/openapi/', function () {
    return view('docs.openapi.index');
});

Route::get('/docs/openapi/openapi.yaml', function () {
    return response()->file(base_path('docs/openapi/openapi.yaml'), [
        'Content-Type' => 'application/x-yaml',
    ]);
});

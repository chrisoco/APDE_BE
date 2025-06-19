<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/tokens/create', function (Request $request) {
    return $request->all(); // TODO: remove this
    $token = $request->user()->createToken($request->token_name, ['view-movies']);

    return ['token' => $token->plainTextToken];
});
// TODO: https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/user-authentication/#create-the-user-controller
Route::post('/tokens/fake', function (Request $request) {
    $token = App\Models\User::first()->createToken('idk', ['view-movies']);

    return ['token' => $token->plainTextToken];
});

Route::get('/movies', function () {
    // Token has ability "view-movies"
    return App\Models\Movie::all();
})->middleware(['auth:sanctum', 'abilities:view-movies']);

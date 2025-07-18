<?php

use App\Http\Controllers\PokemonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong',
        'status' => true
    ]);
});

Route::get("/", [PokemonController::class, 'getAll']);
Route::get("/{name}", [PokemonController::class, 'getByName']);

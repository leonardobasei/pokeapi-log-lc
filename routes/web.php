<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index');
});
Route::get('/{name}', function () {
    return Inertia::render('Pokemon');
});

require __DIR__.'/auth.php';

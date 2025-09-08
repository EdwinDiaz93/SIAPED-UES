<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('dashboard');
})->name('dashboard');

Route::get('labor-academica', function () {
    return Inertia::render('LaborAcademica/Labor');
})->name('labor-academica');



// cuando este la autenticacion
Route::middleware(['auth', 'verified'])->group(function () {});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

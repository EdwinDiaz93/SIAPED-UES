<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::group(['middleware' => ['permission:account.details']], function () {
        Route::livewire('/cuenta',"pages::account_details")->name('account.details');
    });
});

require __DIR__ . '/settings.php';

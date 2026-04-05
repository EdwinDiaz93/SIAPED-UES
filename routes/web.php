<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');


    Route::livewire('/cuenta', "pages::account_details")->middleware('permission:account.details')->name('account.details');
    Route::livewire('/usuarios', "pages::users")->middleware('permission:manage.users')->name('manage.users');
});

require __DIR__ . '/settings.php';

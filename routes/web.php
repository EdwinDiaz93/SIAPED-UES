<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');


    Route::livewire('/cuenta', "pages::account_details")->middleware('permission:account.details')->name('account.details');
    Route::livewire('/usuarios', "pages::users.index")->middleware('permission:manage.users')->name('manage.users');
    Route::livewire('/usuarios/informacion', "pages::users.info")->middleware('permission:manage.users')->name('users.info');

    Route::livewire('/periodos', "pages::periodos.index")->middleware('permission:manage.periodos')->name('manage.periodos');

    Route::livewire('/evaluaciones', "pages::evaluaciones.index")->middleware('permission:manage.evaluaciones')->name('manage.evaluaciones');
    Route::livewire('/evaluaciones/cuestionario', "pages::evaluaciones.cuestionario")->middleware('permission:manage.evaluaciones')->name('evaluaciones.cuestionario');

    Route::livewire('/credenciales', "pages::credenciales.index")->middleware('permission:fill.credenciales')->name('credenciales');

    Route::livewire('/formulario', "pages::formulario.show")->middleware('permission:manage.evaluaciones|fill.credenciales')->name('formulario.show');

    Route::livewire('/reportes', "pages::reportes.index")->middleware('permission:manage.reportes')->name('reportes');

    Route::livewire('/promociones', "pages::promociones.index")->middleware('permission:manage.promociones')->name('promociones');
});

require __DIR__ . '/settings.php';

<?php

use Illuminate\Support\Facades\Route;


Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'))->name('home');

Route::livewire('/solicitud-reactivacion', "pages::auth.solicitud-reactivacion")
    ->middleware('guest')
    ->name('reactivacion.solicitar');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => auth()->user()->hasAnyRole(['admin', 'comite'])
        ? view('dashboard')
        : redirect()->route('account.details'))->name('dashboard');


    Route::livewire('/cuenta', "pages::account_details")->middleware('permission:account.details')->name('account.details');
    Route::livewire('/usuarios', "pages::users.index")->middleware('permission:manage.users')->name('manage.users');
    Route::livewire('/usuarios/informacion', "pages::users.info")->middleware('permission:manage.users')->name('users.info');

    Route::livewire('/periodos', "pages::periodos.index")->middleware('permission:manage.periodos')->name('manage.periodos');

    Route::livewire('/evaluaciones', "pages::evaluaciones.index")->middleware('permission:manage.evaluaciones')->name('manage.evaluaciones');
    Route::livewire('/evaluaciones/cuestionario', "pages::evaluaciones.cuestionario")->middleware('permission:manage.evaluaciones|fill.cuestionario.auto')->name('evaluaciones.cuestionario');

    Route::livewire('/credenciales', "pages::credenciales.index")->middleware('permission:fill.credenciales|manage.users')->name('credenciales');
    Route::livewire('/credenciales/revision', "pages::credenciales.revision")->middleware('permission:manage.users')->name('credenciales.revision');

    Route::livewire('/formulario', "pages::formulario.show")->middleware('permission:manage.evaluaciones|fill.credenciales')->name('formulario.show');

    Route::livewire('/reportes', "pages::reportes.index")->middleware('permission:manage.reportes')->name('reportes');
    Route::livewire('/reportes/promocion', "pages::reportes.promocion")->middleware('permission:reportes.promocion')->name('reportes.promocion');
    Route::livewire('/reportes/atestados', "pages::reportes.atestados")->middleware('permission:reportes.atestados')->name('reportes.atestados');

    Route::livewire('/promociones', "pages::promociones.index")->middleware('permission:manage.promociones')->name('promociones');

    Route::livewire('/catalogos', "pages::catalogos.index")->middleware('permission:manage.catalogos')->name('manage.catalogos');

    Route::livewire('/auditoria', "pages::auditoria.index")->middleware('permission:manage.auditoria')->name('manage.auditoria');
});

require __DIR__ . '/settings.php';

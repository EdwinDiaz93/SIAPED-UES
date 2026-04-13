<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablas = [
            'credenciales_capacitacion',
            'credenciales_proyeccion_social',
            'credenciales_especializacion',
            'credenciales_investigacion',
            'credenciales_seguimiento',
        ];

        foreach ($tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
                      ->default('pendiente')
                      ->after('puntaje');
            });
        }
    }

    public function down(): void
    {
        $tablas = [
            'credenciales_capacitacion',
            'credenciales_proyeccion_social',
            'credenciales_especializacion',
            'credenciales_investigacion',
            'credenciales_seguimiento',
        ];

        foreach ($tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }
    }
};

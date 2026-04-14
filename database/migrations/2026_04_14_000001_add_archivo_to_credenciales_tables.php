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
                $table->string('archivo_path')->nullable()->after('estado');
                $table->string('archivo_descripcion')->nullable()->after('archivo_path');
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
                $table->dropColumn(['archivo_path', 'archivo_descripcion']);
            });
        }
    }
};

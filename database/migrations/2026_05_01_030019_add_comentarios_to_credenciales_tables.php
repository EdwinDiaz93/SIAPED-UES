<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'credenciales_capacitacion',
        'credenciales_proyeccion_social',
        'credenciales_especializacion',
        'credenciales_investigacion',
        'credenciales_seguimiento',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->text('comentario')->nullable()->after('archivo_descripcion');
                $t->text('comentario_rechazo')->nullable()->after('comentario');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['comentario', 'comentario_rechazo']);
            });
        }
    }
};

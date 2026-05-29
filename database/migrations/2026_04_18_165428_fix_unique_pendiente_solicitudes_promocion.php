<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
          // 1. Eliminar el índice antiguo de forma segura usando Laravel Schema
        Schema::table('solicitudes_promocion', function (Blueprint $table) {
            $table->dropUnique('unique_pendiente'); 
        });

        // 2. Crear la columna virtual compatible con MySQL para simular el índice parcial
        DB::statement("ALTER TABLE solicitudes_promocion ADD COLUMN `pendiente_docente_id` BIGINT UNSIGNED 
            GENERATED ALWAYS AS (IF(estado = 'pendiente', docente_id, NULL)) VIRTUAL");

        // 3. Aplicar el índice único sobre la columna virtual
        Schema::table('solicitudes_promocion', function (Blueprint $table) {
            $table->unique('pendiente_docente_id', 'unique_pendiente_filtrado');
        });
    }

    public function down(): void
    {
            Schema::table('solicitudes_promocion', function (Blueprint $table) {
            $table->dropUnique('unique_pendiente_filtrado');
        });

        DB::statement("ALTER TABLE solicitudes_promocion DROP COLUMN `pendiente_docente_id`");

        Schema::table('solicitudes_promocion', function (Blueprint $table) {
            $table->unique(['docente_id', 'estado'], 'unique_pendiente');
        });
    }
};

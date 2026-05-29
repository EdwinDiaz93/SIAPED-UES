<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_promocion', function (Blueprint $table) {
            // 1. Crear un índice normal de respaldo para que la clave foránea no quede desprotegida
            $table->index('docente_id', 'solicitudes_promocion_docente_id_index');

            // 2. Ahora MySQL ya te dejará borrar el índice único antiguo sin problemas
            $table->dropUnique('unique_pendiente');
        });

        // 3. Crear la columna virtual compatible con MySQL para simular el índice parcial
        DB::statement("ALTER TABLE solicitudes_promocion ADD COLUMN `pendiente_docente_id` BIGINT UNSIGNED 
        GENERATED ALWAYS AS (IF(estado = 'pendiente', docente_id, NULL)) VIRTUAL");

        // 4. Aplicar el índice único sobre la columna virtual
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

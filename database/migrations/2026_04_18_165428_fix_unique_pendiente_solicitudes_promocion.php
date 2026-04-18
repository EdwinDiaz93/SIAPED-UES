<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar constraint incorrecto que impide múltiples aprobadas/rechazadas
        DB::statement('ALTER TABLE solicitudes_promocion DROP CONSTRAINT IF EXISTS unique_pendiente');

        // Índice parcial: solo 1 solicitud pendiente por docente a la vez
        DB::statement('CREATE UNIQUE INDEX unique_pendiente ON solicitudes_promocion (docente_id) WHERE estado = \'pendiente\'');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS unique_pendiente');

        DB::statement('ALTER TABLE solicitudes_promocion ADD CONSTRAINT unique_pendiente UNIQUE (docente_id, estado)');
    }
};

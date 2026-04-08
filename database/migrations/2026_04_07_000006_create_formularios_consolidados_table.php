<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formularios_consolidados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('periodo_id')->nullable()->constrained('periodos_evaluacion')->onDelete('set null');
            $table->foreignId('generado_por')->nullable()->constrained('users')->onDelete('set null');

            // Snapshot del cálculo
            $table->json('aspectos');            // array completo de puntajes por aspecto
            $table->decimal('total_ganado', 6, 2);
            $table->decimal('total_maximo', 6, 2);
            $table->string('categoria_actual', 10);
            $table->string('siguiente_categoria', 10)->nullable();
            $table->boolean('cumple_ascenso')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formularios_consolidados');
    }
};

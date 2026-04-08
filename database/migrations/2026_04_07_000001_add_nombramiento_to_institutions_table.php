<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->date('fecha_ingreso')->nullable()->after('fecha_graduacion');
            $table->foreignId('tipo_nombramiento_id')
                ->nullable()
                ->after('fecha_ingreso')
                ->constrained('catalog_values')
                ->onDelete('set null')
                ->onUpdate('set null');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropForeign(['tipo_nombramiento_id']);
            $table->dropColumn(['fecha_ingreso', 'tipo_nombramiento_id']);
        });
    }
};

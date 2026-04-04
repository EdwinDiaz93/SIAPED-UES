<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('grado_id')->constrained('catalog_values')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('institucion_id')->constrained('catalog_values')->onDelete('cascade')->onUpdate('cascade');
            $table->date('fecha_graduacion');
            $table->foreignId('escuela_id')->constrained('catalog_values')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('categoria_id')->constrained('catalog_values')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('area_id')->constrained('catalog_values')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};

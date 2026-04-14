<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellidos')->nullable()->change();
            $table->date('fecha_nacimiento')->nullable()->change();
            $table->unsignedBigInteger('sexo')->nullable()->change();
            $table->unsignedBigInteger('nacionalidad')->nullable()->change();
            $table->unsignedBigInteger('estado_civil')->nullable()->change();
            $table->text('direccion')->nullable()->change();
            $table->string('telefono')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellidos')->nullable(false)->change();
            $table->date('fecha_nacimiento')->nullable(false)->change();
            $table->unsignedBigInteger('sexo')->nullable(false)->change();
            $table->unsignedBigInteger('nacionalidad')->nullable(false)->change();
            $table->unsignedBigInteger('estado_civil')->nullable(false)->change();
            $table->text('direccion')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
        });
    }
};

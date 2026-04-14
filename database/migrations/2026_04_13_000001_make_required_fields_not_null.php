<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla users: campos personales obligatorios
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellidos')->nullable(false)->change();
            $table->date('fecha_nacimiento')->nullable(false)->change();
            $table->unsignedBigInteger('sexo')->nullable(false)->change();
            $table->unsignedBigInteger('nacionalidad')->nullable(false)->change();
            $table->unsignedBigInteger('estado_civil')->nullable(false)->change();
            $table->text('direccion')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
        });

        // Tabla institutions: fecha_ingreso y tipo_nombramiento obligatorios
        Schema::table('institutions', function (Blueprint $table) {
            $table->date('fecha_ingreso')->nullable(false)->change();
            $table->unsignedBigInteger('tipo_nombramiento_id')->nullable(false)->change();
        });

        // Tabla documents: expedicion y expiracion obligatorios
        Schema::table('documents', function (Blueprint $table) {
            $table->date('fecha_expedicion')->nullable(false)->change();
            $table->string('lugar_expedicion')->nullable(false)->change();
            $table->date('fecha_expiracion')->nullable(false)->change();
        });
    }

    public function down(): void
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

        Schema::table('institutions', function (Blueprint $table) {
            $table->date('fecha_ingreso')->nullable()->change();
            $table->unsignedBigInteger('tipo_nombramiento_id')->nullable()->change();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->date('fecha_expedicion')->nullable()->change();
            $table->string('lugar_expedicion')->nullable()->change();
            $table->date('fecha_expiracion')->nullable()->change();
        });
    }
};

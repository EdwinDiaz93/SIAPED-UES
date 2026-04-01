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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId("document_type_id")->constrained("catalog_values")->onDelete('cascade')->onUpdate('cascade');
            $table->string("value");
            $table->date("fecha_expedicion")->nullable();
            $table->string("lugar_expedicion")->nullable();
            $table->date("fecha_expiracion")->nullable();
            $table->string("institucion")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

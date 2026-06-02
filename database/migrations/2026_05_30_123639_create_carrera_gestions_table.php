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
        Schema::create('carrera_gestion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_carrera', 20);
            $table->string('codigo_gestion', 20);
            $table->unsignedSmallInteger('cupos')->default(0);

            $table->unique(['codigo_carrera', 'codigo_gestion']);

            $table->foreign('codigo_carrera')->references('codigo')->on('carrera')->onDelete('cascade');
            $table->foreign('codigo_gestion')->references('codigo')->on('gestion')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera_gestion');
    }
};

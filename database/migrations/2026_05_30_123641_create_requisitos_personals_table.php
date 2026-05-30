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
        Schema::create('requisitos_personal', function (Blueprint $table) {
            $table->id(); // Aunque sea compuesta la PK en el DDL original, agregamos ID para facilitar Eloquent
            $table->string('registro_personal', 20);
            $table->string('area', 20)->nullable();
            $table->string('nivel_grado', 20)->nullable();
            $table->string('nivel_exp', 20)->nullable();
            $table->string('maestria', 50);
            $table->string('doctorado', 50);
            $table->string('diplomado', 50);

            $table->foreign('registro_personal')->references('registro')->on('personal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitos_personal');
    }
};

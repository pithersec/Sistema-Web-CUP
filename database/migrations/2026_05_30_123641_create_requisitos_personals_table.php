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
            $table->enum('area', ['matematicas', 'fisica', 'computacion', 'ingles', 'administracion', 'sistemas', 'otra'])->nullable();
            $table->enum('nivel_grado', ['tecnico_medio', 'tecnico_superior', 'licenciatura', 'ingenieria', 'maestria', 'doctorado'])->nullable();
            $table->smallInteger('nivel_exp')->nullable();
            $table->boolean('maestria')->default(false);
            $table->boolean('doctorado')->default(false);
            $table->boolean('diplomado')->default(false);

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

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
        Schema::create('asistencia', function (Blueprint $table) {
            $table->date('fecha');
            $table->boolean('presente')->default(false);
            $table->string('codigo_postulante', 20);
            $table->string('codigo_gestion', 20);
            $table->string('id_grupo', 10);
            $table->unsignedBigInteger('id_materia');

            $table->primary(['fecha', 'codigo_postulante', 'codigo_gestion', 'id_grupo', 'id_materia']);

            $table->foreign('codigo_postulante')->references('codigo')->on('postulante')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_materia')->references('id')->on('materia')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_grupo', 'codigo_gestion'])->references(['id', 'codigo_gestion'])->on('grupo')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia');
    }
};

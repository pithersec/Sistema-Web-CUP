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
        Schema::create('grupo_materia', function (Blueprint $table) {
            $table->string('id_grupo', 10);
            $table->string('gestion_grupo', 20);
            $table->foreignId('id_materia')->constrained('materia')->onUpdate('cascade')->onDelete('restrict');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->unsignedSmallInteger('orden')->nullable();
            $table->string('registro_personal', 20)->nullable();

            $table->primary(['id_materia', 'id_grupo', 'gestion_grupo']);
            $table->foreign(['id_grupo', 'gestion_grupo'])->references(['id', 'codigo_gestion'])->on('grupo')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('registro_personal')->references('registro')->on('personal')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_materia');
    }
};

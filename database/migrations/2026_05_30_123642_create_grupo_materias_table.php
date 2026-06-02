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
            $table->id();
            $table->foreignId('id_materia')->constrained('materia')->onDelete('cascade');
            $table->foreignId('id_grupo')->constrained('grupo')->onDelete('cascade');
            $table->string('horario', 100)->nullable();
            $table->string('registro_personal', 20)->nullable();

            $table->unique(['id_materia', 'id_grupo']);
            $table->foreign('registro_personal')->references('registro')->on('personal')->onDelete('set null');
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

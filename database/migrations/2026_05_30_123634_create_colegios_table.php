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
        Schema::create('colegio', function (Blueprint $table) {
            $table->id(); // Genera automáticamente un BIGSERIAL PRIMARY KEY
            $table->string('cie', 20);
            $table->string('nombre', 150);
            $table->enum('tipo', ['fiscal', 'particular', 'convenio', 'privado', 'extranjero'])->nullable();
            $table->enum('turno', ['mañana', 'tarde', 'noche'])->nullable();
            $table->string('pais', 80)->nullable();
            $table->string('departamento', 80)->nullable();
            $table->string('provincia', 80)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colegio');
    }
};

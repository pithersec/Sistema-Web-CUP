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
        Schema::create('carrera', function (Blueprint $table) {
            $table->string('codigo', 10)->primary(); // PK String
            $table->string('plan', 5)->nullable();
            $table->string('nombre', 100);
            $table->enum('modalidad', ['presencial', 'virtual'])->nullable();
            $table->enum('nivel', ['licenciatura', 'tecnico_superior', 'tecnico_medio'])->nullable();
            $table->enum('tipo', ['semestral', 'anual'])->nullable();
            $table->unsignedTinyInteger('duracion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera');
    }
};

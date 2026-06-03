<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carrera', function (Blueprint $table) {
            $table->string('codigo', 20);
            $table->string('plan', 50);
            $table->string('nombre', 100);
            $table->enum('modalidad', ['presencial', 'virtual']);
            $table->enum('nivel', ['licenciatura', 'tecnico_superior', 'tecnico_medio'])->nullable();
            $table->enum('tipo', ['semestral', 'anual'])->nullable();
            $table->unsignedTinyInteger('duracion')->nullable();
            $table->primary(['codigo', 'plan', 'modalidad']);
        });

        DB::statement('ALTER TABLE carrera ADD CONSTRAINT carrera_codigo_plan_modalidad_unique UNIQUE (codigo, plan, modalidad)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera');
    }
};

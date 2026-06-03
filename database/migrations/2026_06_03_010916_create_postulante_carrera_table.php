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
        Schema::create('postulante_carrera', function (Blueprint $table) {
            $table->string('codigo_postulante', 20);
            $table->string('codigo_carrera', 20);
            $table->string('plan_carrera', 50);
            $table->string('modalidad_carrera', 50);
            $table->unsignedSmallInteger('opcion');

            $table->primary(['codigo_postulante', 'codigo_carrera', 'plan_carrera', 'modalidad_carrera']);

            $table->foreign('codigo_postulante')
                ->references('codigo')->on('postulante')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign(['codigo_carrera', 'plan_carrera', 'modalidad_carrera'])
                ->references(['codigo', 'plan', 'modalidad'])
                ->on('carrera')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulante_carrera');
    }
};

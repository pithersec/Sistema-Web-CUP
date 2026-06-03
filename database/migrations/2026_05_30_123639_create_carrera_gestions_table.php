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
            $table->string('codigo_carrera', 20);
            $table->string('plan_carrera', 50);
            $table->string('modalidad_carrera', 50);
            $table->string('codigo_gestion', 20);
            $table->unsignedSmallInteger('cupos')->default(0);

            $table->primary(['codigo_carrera', 'plan_carrera', 'modalidad_carrera', 'codigo_gestion']);

            $table->foreign(['codigo_carrera', 'plan_carrera', 'modalidad_carrera'], 'fk_cg_carrera')
                ->references(['codigo', 'plan', 'modalidad'])
                ->on('carrera')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('codigo_gestion')
                ->references('codigo')->on('gestion')
                ->onUpdate('cascade')->onDelete('cascade');
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

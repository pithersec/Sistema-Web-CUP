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
        Schema::create('postulante', function (Blueprint $table) {
            $table->string('codigo', 20)->primary();
            $table->string('ci', 20);
            $table->string('procedencia', 100)->nullable();
            $table->string('telefono_2', 20)->nullable();
            $table->date('plazo')->nullable();
            $table->string('estado', 30)->default('preinscrito');
            $table->string('gestion_egreso', 20)->nullable();
            $table->string('id_grupo', 10)->nullable();

            $table->foreignId('id_requisitos_postulante')->nullable()->constrained('requisitos_postulante')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('id_colegio')->nullable()->constrained('colegio')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('id_pago')->nullable()->constrained('pago')->onUpdate('cascade')->onDelete('set null');

            $table->foreign('id_grupo')->references('id')->on('grupo')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('ci')->references('ci')->on('datos_personales')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulante');
    }
};

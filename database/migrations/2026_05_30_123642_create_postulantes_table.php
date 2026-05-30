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
            $table->string('codigo', 20)->primary(); // PK del Postulante
            $table->string('ci', 20);
            $table->string('procedencia', 100)->nullable();
            $table->string('telefono_2', 20)->nullable();
            $table->date('plazo')->nullable();
            $table->string('estado', 30)->default('preinscrito');
            $table->string('gestion_egreso', 20)->nullable();
            
            // Llaves foráneas con IDs enteros
            $table->foreignId('id_requisitos_postulante')->nullable()->constrained('requisitos_postulante')->onDelete('set null');
            $table->foreignId('id_colegio')->nullable()->constrained('colegio')->onDelete('restrict');
            $table->foreignId('id_pago')->nullable()->constrained('pago')->onDelete('set null');
            $table->foreignId('id_grupo')->nullable()->constrained('grupo')->onDelete('set null');
            
            // Llaves foráneas que apuntan a Códigos String (Carreras)
            $table->string('codigo_carrera1', 20)->nullable();
            $table->string('codigo_carrera2', 20)->nullable();
            
            // Atributo extra del control de cupos (Lo que definimos al inicio)
            // $table->string('carrera_admitida_id', 20)->nullable();

            // Restricciones de relaciones String
            $table->foreign('ci')->references('ci')->on('datos_personales')->onDelete('cascade');
            $table->foreign('codigo_carrera1')->references('codigo')->on('carrera')->onDelete('restrict');
            $table->foreign('codigo_carrera2')->references('codigo')->on('carrera')->onDelete('restrict');
            // $table->foreign('carrera_admitida_id')->references('codigo')->on('carrera')->onDelete('restrict');
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

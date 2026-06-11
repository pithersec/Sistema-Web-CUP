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
        Schema::create('reclamo', function (Blueprint $table) {
            $table->id(); // SERIAL PRIMARY KEY
            $table->text('descripcion');
            $table->timestamp('fecha')->useCurrent();
            $table->string('dirigido', 200)->nullable();
            $table->enum('estado', ['pendiente', 'atendido', 'rechazado'])->default('pendiente');
            
            $table->string('codigo_postulante', 20);
            $table->string('registro_personal', 20)->nullable(); // Funcionario encargado de resolverlo

            // Restricciones de llaves foráneas String
            $table->foreign('codigo_postulante')->references('codigo')->on('postulante')->onDelete('cascade');
            $table->foreign('registro_personal')->references('registro')->on('personal')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamo');
    }
};

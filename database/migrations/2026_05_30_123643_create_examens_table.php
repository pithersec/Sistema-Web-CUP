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
        Schema::create('examen', function (Blueprint $table) {
            $table->id(); // ID estándar para facilitar las operaciones de Eloquent
            $table->string('codigo_postulante', 20);
            $table->integer('nro_examen');
            $table->decimal('ponderacion', 5, 2);
            $table->decimal('nota', 5, 2)->nullable();
            $table->date('fecha')->nullable();
            $table->foreignId('id_materia')->constrained('materia')->onDelete('restrict');

            // Llave foránea String hacia Postulante
            $table->foreign('codigo_postulante')->references('codigo')->on('postulante')->onDelete('cascade');

            // Regla de negocio: Un postulante no puede repetir el mismo número de examen en la misma materia
            $table->unique(['codigo_postulante', 'nro_examen', 'id_materia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen');
    }
};

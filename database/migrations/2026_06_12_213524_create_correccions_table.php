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
        Schema::create('correccion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_postulante', 20);
            $table->unsignedBigInteger('id_materia');
            $table->unsignedSmallInteger('nro_examen');
            $table->decimal('nota_anterior', 5, 2);
            $table->decimal('nota_nueva', 5, 2);
            $table->text('justificacion');
            $table->string('registro_personal', 20);
            $table->timestamp('fecha')->useCurrent();

            $table->foreign(['codigo_postulante', 'id_materia', 'nro_examen'])
                ->references(['codigo_postulante', 'id_materia', 'nro_examen'])
                ->on('examen')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('registro_personal')
                ->references('registro')->on('personal')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correccion');
    }
};

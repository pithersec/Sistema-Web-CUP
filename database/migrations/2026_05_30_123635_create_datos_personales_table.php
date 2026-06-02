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
        Schema::create('datos_personales', function (Blueprint $table) {
            $table->string('ci', 11)->primary();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->enum('genero', ['m', 'f']);
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 150);
            $table->date('fecha_nac');
            $table->string('direccion', 200);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_personales');
    }
};

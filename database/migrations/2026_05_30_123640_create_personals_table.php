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
        Schema::create('personal', function (Blueprint $table) {
            $table->string('registro', 20)->primary(); // Código de registro de trabajador (PK)
            $table->boolean('estado')->default(true); // Estado del trabajador (activo/inactivo)
            $table->string('ci', 11);
            
            $table->foreign('ci')->references('ci')->on('datos_personales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};

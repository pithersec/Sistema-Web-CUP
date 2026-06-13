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
        Schema::create('gestion', function (Blueprint $table) {
            $table->string('codigo', 20)->primary(); // PK Personalizada tipo String
            $table->date('fecha_ini');
            $table->date('fecha_fin');
            $table->date('fecha_inicio_notas')->nullable();
            $table->date('fecha_fin_notas')->nullable();
            $table->unsignedTinyInteger('nota_minima')->default(60);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestion');
    }
};

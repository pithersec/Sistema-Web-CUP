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
        Schema::create('carrera', function (Blueprint $table) {
            $table->string('codigo', 10)->primary(); // PK String
            $table->string('plan', 5)->nullable();
            $table->string('nombre', 100);
            $table->string('modalidad', 10)->nullable();
            $table->string('nivel', 20)->nullable();
            $table->string('tipo', 50)->nullable();
            $table->string('duracion',20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera');
    }
};

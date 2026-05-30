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
        Schema::create('perfil_privilegio', function (Blueprint $table) {
            $table->foreignId('id_perfil')->constrained('perfil')->onDelete('cascade');
            $table->foreignId('id_privilegio')->constrained('privilegio')->onDelete('cascade');
            $table->primary(['id_perfil', 'id_privilegio']); // Compuesta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_privilegio');
    }
};

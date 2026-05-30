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
        Schema::create('grupo', function (Blueprint $table) {
            $table->id(); // SERIAL PRIMARY KEY
            $table->string('aula', 10)->nullable();
            $table->enum('turno', ['mañana', 'tarde', 'noche'])->nullable();
            $table->string('horario', 100)->nullable();
            $table->unsignedSmallInteger('total_ins')->default(0);
            $table->string('codigo_gestion', 20);
            
            // Llave foránea que apunta a la gestión académica
            $table->foreign('codigo_gestion')->references('codigo')->on('gestion')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo');
    }
};

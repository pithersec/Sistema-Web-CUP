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
            $table->string('id', 10);
            $table->string('aula', 10)->nullable();
            $table->unsignedSmallInteger('total_ins')->default(0);
            $table->string('codigo_gestion', 20);
            $table->string('nombre_turno', 20)->nullable();

            $table->primary(['id', 'codigo_gestion']);

            $table->foreign('codigo_gestion')->references('codigo')->on('gestion')
                ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('nombre_turno')->references('nombre')->on('turno')
                ->onUpdate('cascade')->onDelete('set null');
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

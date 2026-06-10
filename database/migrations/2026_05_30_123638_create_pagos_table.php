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
        Schema::create('pago', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto', 10, 2);
            $table->timestamp('fecha');
            $table->string('concepto', 200)->nullable();
            $table->enum('estado', ['pendiente', 'completado', 'rechazado', 'anulado'])->default('pendiente');
            $table->string('id_transaccion', 200)->nullable();
            $table->string('moneda', 10)->default('USD');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago');
    }
};

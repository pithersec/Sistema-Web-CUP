<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pago')->insert([
            ['id' => 1, 'monto' => 300.00, 'fecha' => '2025-01-10', 'concepto' => 'Inscripción CUP 1-2025', 'estado' => 'completado', 'referencia_pasarela' => 'PAGO-BANCO-8821'],
            ['id' => 2, 'monto' => 300.00, 'fecha' => '2025-07-12', 'concepto' => 'Inscripción CUP 2-2025', 'estado' => 'completado', 'referencia_pasarela' => 'PAGO-BANCO-9943'],
            ['id' => 3, 'monto' => 350.00, 'fecha' => '2026-01-15', 'concepto' => 'Inscripción CUP 1-2026', 'estado' => 'completado', 'referencia_pasarela' => 'PAGO-QR-1102'],
        ]);
    }
}

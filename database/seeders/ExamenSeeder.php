<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('examen')->insert([
            // Notas aprobadas del Postulante 1 (Gestión 1-2025)
            [
                'codigo_postulante' => 'POST-20251-01',
                'nro_examen' => 1,
                'ponderacion' => 30.00,
                'nota' => 85.50,
                'fecha' => '2025-02-15',
                'id_materia' => 1 
            ],
            [
                'codigo_postulante' => 'POST-20251-01',
                'nro_examen' => 2,
                'ponderacion' => 30.00,
                'nota' => 78.00,
                'fecha' => '2025-02-20',
                'id_materia' => 2 
            ],
            [
                'codigo_postulante' => 'POST-20251-01',
                'nro_examen' => 3,
                'ponderacion' => 40.00,
                'nota' => 72.00,
                'fecha' => '2025-03-01',
                'id_materia' => 3
            ],

            // Nota reprobada del Postulante 2 (Gestión 2-2025)
            [
                'codigo_postulante' => 'POST-20252-02',
                'nro_examen' => 1,
                'ponderacion' => 30.00,
                'nota' => 45.00,
                'fecha' => '2025-08-18',
                'id_materia' => 1
            ],

            // Primera nota del Postulante 3 (Gestión Activa 1-2026)
            [
                'codigo_postulante' => 'POST-20261-03',
                'nro_examen' => 1,
                'ponderacion' => 30.00,
                'nota' => 68.00,
                'fecha' => '2026-02-14',
                'id_materia' => 2 
            ],
        ]);
    }
}
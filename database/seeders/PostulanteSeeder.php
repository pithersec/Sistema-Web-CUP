<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostulanteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('postulante')->insert([
            // Gestión 1-2025 (Histórico - Ya admitido)
            [
                'codigo' => 'POST-20251-01',
                'ci' => '9000001',
                'procedencia' => 'Santa Cruz de la Sierra',
                'telefono_2' => '60011111',
                'plazo' => '2025-01-30',
                'estado' => 'admitido',
                'gestion_egreso' => '2024',
                'id_requisitos_postulante' => 1,
                'id_colegio' => 1,
                'id_pago' => 1,
                'id_grupo' => 1, 
                'codigo_carrera1' => '187-3', 
                'codigo_carrera2' => '187-4', 
            ],
            // Gestión 2-2025 (Histórico - Reprobado)
            [
                'codigo' => 'POST-20252-02',
                'ci' => '9000002',
                'procedencia' => 'Montero',
                'telefono_2' => '60022222',
                'plazo' => '2025-07-28',
                'estado' => 'reprobado',
                'gestion_egreso' => '2024',
                'id_requisitos_postulante' => 2,
                'id_colegio' => 2,
                'id_pago' => 2,
                'id_grupo' => 3, 
                'codigo_carrera1' => '187-5', 
                'codigo_carrera2' => '187-3', 
            ],
            // Gestión 1-2026 (Activo - Preinscrito actualmente)
            [
                'codigo' => 'POST-20261-03',
                'ci' => '9000003',
                'procedencia' => 'Santa Cruz de la Sierra',
                'telefono_2' => '60033333',
                'plazo' => '2026-01-29',
                'estado' => 'preinscrito',
                'gestion_egreso' => '2025',
                'id_requisitos_postulante' => 3,
                'id_colegio' => 1,
                'id_pago' => 3,
                'id_grupo' => 4, 
                'codigo_carrera1' => '187-3', 
                'codigo_carrera2' => '187-4', 
            ],
        ]);
    }
}
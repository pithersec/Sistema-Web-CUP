<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraGestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carrera_gestion')->insert([
            // Gestión 1-2025 (Cupos estándar iniciales)
            ['codigo_carrera' => '187-3', 'codigo_gestion' => '1-2025', 'cupos' => 120],
            ['codigo_carrera' => '187-4', 'codigo_gestion' => '1-2025', 'cupos' => 100],
            ['codigo_carrera' => '187-5', 'codigo_gestion' => '1-2025', 'cupos' => 80],

            // Gestión 2-2025 (Reducción por temporada baja)
            ['codigo_carrera' => '187-3', 'codigo_gestion' => '2-2025', 'cupos' => 60],
            ['codigo_carrera' => '187-4', 'codigo_gestion' => '2-2025', 'cupos' => 50],
            ['codigo_carrera' => '187-5', 'codigo_gestion' => '2-2025', 'cupos' => 40],

            // Gestión 1-2026 (Ampliación por alta demanda actual)
            ['codigo_carrera' => '187-3', 'codigo_gestion' => '1-2026', 'cupos' => 150],
            ['codigo_carrera' => '187-4', 'codigo_gestion' => '1-2026', 'cupos' => 120],
            ['codigo_carrera' => '187-5', 'codigo_gestion' => '1-2026', 'cupos' => 90],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraGestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carrera_gestion')->insert([
            // 1-2025
            ['codigo_carrera' => '187', 'plan_carrera' => '4', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2025', 'cupos' => 150],
            ['codigo_carrera' => '187', 'plan_carrera' => '6', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2025', 'cupos' => 130],
            ['codigo_carrera' => '187', 'plan_carrera' => '5', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2025', 'cupos' => 100],
            ['codigo_carrera' => '187', 'plan_carrera' => '4', 'modalidad_carrera' => 'virtual',    'codigo_gestion' => '1-2025', 'cupos' => 80],
            ['codigo_carrera' => '187', 'plan_carrera' => '3', 'modalidad_carrera' => 'virtual',    'codigo_gestion' => '1-2025', 'cupos' => 70],
            ['codigo_carrera' => '323', 'plan_carrera' => '0', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2025', 'cupos' => 50],

            // 2-2025
            ['codigo_carrera' => '187', 'plan_carrera' => '4', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '2-2025', 'cupos' => 120],
            ['codigo_carrera' => '187', 'plan_carrera' => '6', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '2-2025', 'cupos' => 100],
            ['codigo_carrera' => '187', 'plan_carrera' => '5', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '2-2025', 'cupos' => 80],
            ['codigo_carrera' => '187', 'plan_carrera' => '4', 'modalidad_carrera' => 'virtual',    'codigo_gestion' => '2-2025', 'cupos' => 65],
            ['codigo_carrera' => '187', 'plan_carrera' => '3', 'modalidad_carrera' => 'virtual',    'codigo_gestion' => '2-2025', 'cupos' => 55],
            ['codigo_carrera' => '323', 'plan_carrera' => '0', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '2-2025', 'cupos' => 40],

            // 1-2026
            ['codigo_carrera' => '187', 'plan_carrera' => '4', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2026', 'cupos' => 180],
            ['codigo_carrera' => '187', 'plan_carrera' => '6', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2026', 'cupos' => 160],
            ['codigo_carrera' => '187', 'plan_carrera' => '5', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2026', 'cupos' => 120],
            ['codigo_carrera' => '187', 'plan_carrera' => '4', 'modalidad_carrera' => 'virtual',    'codigo_gestion' => '1-2026', 'cupos' => 100],
            ['codigo_carrera' => '187', 'plan_carrera' => '3', 'modalidad_carrera' => 'virtual',    'codigo_gestion' => '1-2026', 'cupos' => 90],
            ['codigo_carrera' => '323', 'plan_carrera' => '0', 'modalidad_carrera' => 'presencial', 'codigo_gestion' => '1-2026', 'cupos' => 60],
        ]);
    }
}

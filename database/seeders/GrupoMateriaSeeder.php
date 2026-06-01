<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('grupo_materia')->insert([
            // Gestión 1-2025: Evans dictó Matemáticas en el Grupo 1
            [
                'id_materia' => 1, 
                'id_grupo' => 1, 
                'horario' => '07:00 - 09:15', 
                'registro_personal' => 'REG-DOC03'
            ],
            // Gestión 1-2026: Evans evalúa actualmente Física en el Grupo 4
            [
                'id_materia' => 2, 
                'id_grupo' => 4, 
                'horario' => '07:00 - 09:15', 
                'registro_personal' => 'REG-DOC03'
            ],
        ]);
    }
}

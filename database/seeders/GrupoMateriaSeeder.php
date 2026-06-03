<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('grupo_materia')->insert([
            [
                'id_materia' => 1,
                'id_grupo' => 'M001',
                'horario' => '07:00 - 11:00',
                'registro_personal' => 'REG-DOC03'
            ],
            [
                'id_materia' => 2,
                'id_grupo' => 'M001',
                'horario' => '07:00 - 11:00',
                'registro_personal' => 'REG-DOC03'
            ],
        ]);
    }
}

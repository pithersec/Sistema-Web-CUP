<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilPrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('perfil_privilegio')->insert([
            // Administrador (id: 1) tiene todos los privilegios
            ['id_perfil' => 1, 'id_privilegio' => 1],
            ['id_perfil' => 1, 'id_privilegio' => 2],
            ['id_perfil' => 1, 'id_privilegio' => 3],
            ['id_perfil' => 1, 'id_privilegio' => 4],
            ['id_perfil' => 1, 'id_privilegio' => 5],

            // Ventanilla (id: 2) puede registrar postulantes
            ['id_perfil' => 2, 'id_privilegio' => 2],

            // Docente (id: 3) solo puede registrar notas
            ['id_perfil' => 3, 'id_privilegio' => 4],
        ]);
    }
}

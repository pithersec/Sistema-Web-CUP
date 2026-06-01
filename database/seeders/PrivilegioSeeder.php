<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('privilegio')->insert([
            ['id' => 1, 'nombre' => 'Gestionar Usuarios'],
            ['id' => 2, 'nombre' => 'Registrar Postulante'],
            ['id' => 3, 'nombre' => 'Modificar Cupos'],
            ['id' => 4, 'nombre' => 'Registrar Notas'],
            ['id' => 5, 'nombre' => 'Ver Bitacora'],
        ]);
    }
}

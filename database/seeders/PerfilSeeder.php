<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('perfil')->insert([
            ['id' => 1, 'nombre' => 'Administrador', 'descripcion' => 'Control total del sistema de admisión y auditorías'],
            ['id' => 2, 'nombre' => 'Ventanilla', 'descripcion' => 'Encargado del registro de preinscripciones y requisitos'],
            ['id' => 3, 'nombre' => 'Docente', 'descripcion' => 'Solo lectura de listas e inyección de calificaciones de exámenes'],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('perfil')->insert([
            ['id' => 1, 'nombre' => 'Sistema', 'descripcion' => 'Acceso total al sistema: gestión de usuarios, perfiles, privilegios, configuración avanzada y auditoría'],
            ['id' => 2, 'nombre' => 'Administrador', 'descripcion' => 'Gestión académica: postulantes, docentes, carreras, cupos, grupos y reportes'],
            ['id' => 3, 'nombre' => 'Docente', 'descripcion' => 'Acceso limitado: visualización de sus grupos asignados, registro y edición de notas'],
        ]);
    }
}

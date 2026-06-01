<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuario')->insert([
            [
                'user_name' => 'admin_ficct',
                'clave' => Hash::make('Admin123/*'),
                'email' => 'admin@ficct.uagrm.edu.bo',
                'id_perfil' => 1, // Administrador
                'registro_personal' => 'REG-ADM01'
            ],
            [
                'user_name' => 'ana_ventanilla',
                'clave' => Hash::make('Ventanilla2026'),
                'email' => 'ana.suarez@ficct.uagrm.edu.bo',
                'id_perfil' => 2, // Ventanilla
                'registro_personal' => 'REG-VEN02',
            ],
            [
                'user_name' => 'evans_docente',
                'clave' => Hash::make('DocenteFicct'),
                'email' => 'ebalcazar@ficct.uagrm.edu.bo',
                'id_perfil' => 3, // Docente
                'registro_personal' => 'REG-DOC03',
            ],
        ]);
    }
}
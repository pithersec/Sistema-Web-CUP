<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'user_name' => 'admin_ficct',
            'clave' => 'Admin123/*',
            'email' => 'admin@ficct.uagrm.edu.bo',
            'id_perfil' => 1,
            'registro_personal' => 'REG-ADM01',
        ]);

        Usuario::create([
            'user_name' => 'ana_admin',
            'clave' => 'Admin2026',
            'email' => 'ana.suarez@ficct.uagrm.edu.bo',
            'id_perfil' => 2,
            'registro_personal' => 'REG-VEN02',
        ]);

        Usuario::create([
            'user_name' => 'evans_docente',
            'clave' => 'DocenteFicct',
            'email' => 'ebalcazar@ficct.uagrm.edu.bo',
            'id_perfil' => 3,
            'registro_personal' => 'REG-DOC03',
        ]);

        Usuario::create([
            'user_name' => 'sistema',
            'clave' => 'cvpd2026',
            'email' => 'sistema@ficct.uagrm.edu.bo',
            'id_perfil' => 1,
            'registro_personal' => 'REG-SIS04',
        ]);
    }
}
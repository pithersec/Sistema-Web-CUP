<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;

class SistemaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('datos_personales')->insert([
            'ci'        => '1234567',
            'nombre'    => 'Sistema',
            'apellido'  => 'FICCT',
            'genero'    => 'm',
            'telefono'  => '70000000',
            'correo'    => 'sistema@ficct.uagrm.edu.bo',
            'fecha_nac' => '1990-01-01',
            'direccion' => 'FICCT - UAGRM',
        ]);

        DB::table('personal')->insert([
            'registro' => '1000',
            'ci'       => '1234567',
            'estado'   => true,
        ]);

        DB::table('requisitos_personal')->insert([
            [
                'registro_personal' => '1000',
                'area'        => 'sistemas',
                'nivel_grado' => 'doctorado',
                'nivel_exp'   => 15,
                'maestria'    => true,
                'doctorado'   => true,
                'diplomado'   => true,
            ],
        ]);

        Usuario::create([
            'user_name'         => 'sistema',
            'clave'             => 'sistema2026',
            'email'             => 'sistema@ficct.uagrm.edu.bo',
            'id_perfil'         => 1,
            'registro_personal' => '1000',
        ]);
    }
}
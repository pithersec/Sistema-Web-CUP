<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosPersonalesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('datos_personales')->insert([
            // Personal Administrativo y Docentes
            [
                'ci' => '5432100',
                'nombre' => 'Carlos',
                'apellido' => 'Pérez Ramos',
                'genero' => 'm',
                'telefono' => '71020304',
                'correo' => 'carlos.admin@ficct.uagrm.edu.bo',
                'fecha_nac' => '1985-04-12',
                'direccion' => 'Av. Busch, 2do Anillo'
            ],
            [
                'ci' => '6543210',
                'nombre' => 'Ana',
                'apellido' => 'Suárez Montero',
                'genero' => 'f',
                'telefono' => '72030405',
                'correo' => 'ana.ventanilla@ficct.uagrm.edu.bo',
                'fecha_nac' => '1990-08-22',
                'direccion' => 'Zona Lazareto, C/ Soruco'
            ],
            [
                'ci' => '7654321',
                'nombre' => 'Evans',
                'apellido' => 'Balcázar Veizaga',
                'genero' => 'm',
                'telefono' => '73040506',
                'correo' => 'evans.docente@ficct.uagrm.edu.bo',
                'fecha_nac' => '1978-11-30',
                'direccion' => 'Urb. Las Palmas, C/ 4'
            ],

            // Postulantes Gestión 2025 (Históricos)
            [
                'ci' => '9000001',
                'nombre' => 'Juan Pablo',
                'apellido' => 'Mendoza Ortiz',
                'genero' => 'm',
                'telefono' => '75011111',
                'correo' => 'juan.mendoza@gmail.com',
                'fecha_nac' => '2006-01-15',
                'direccion' => 'Plan 3000, Av. Paurito'
            ],
            [
                'ci' => '9000002',
                'nombre' => 'María René',
                'apellido' => 'Cuéllar Justiniano',
                'genero' => 'f',
                'telefono' => '76022222',
                'correo' => 'maria.cuellar@hotmail.com',
                'fecha_nac' => '2006-05-20',
                'direccion' => 'Villa Primero de Mayo, C/ 3'
            ],

            // Postulantes Gestión 2026 (Actuales)
            [
                'ci' => '9000003',
                'nombre' => 'Luis Fernando',
                'apellido' => 'Gómez Torrico',
                'genero' => 'm',
                'telefono' => '77033333',
                'correo' => 'luis.gomez.t@gmail.com',
                'fecha_nac' => '2007-02-10',
                'direccion' => 'Av. Banzer, 4to Anillo'
            ],
        ]);
    }
}
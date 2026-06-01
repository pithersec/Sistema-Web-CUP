<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReclamoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reclamo')->insert([
            [
                'descripcion' => 'El postulante solicita revisión manual de la pregunta de vectores en el examen #1.',
                'fecha' => '2025-02-16 11:00:00',
                'dirigido' => 'Jefatura de Admisión FICCT',
                'codigo_postulante' => 'POST-20251-01',
                'registro_personal' => 'REG-ADM01'
            ]
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequisitosPersonalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('requisitos_personal')->insert([
            [
                'registro_personal' => 'REG-DOC03', // Evans Balcázar
                'area' => 'Sistemas',
                'nivel_grado' => 'Licenciatura',
                'nivel_exp' => '15 años',
                'maestria' => 'Maestría en Ciencias de la Computación',
                'doctorado' => 'No presenta',
                'diplomado' => 'Diplomado en Educación Superior'
            ],
            [
                'registro_personal' => 'REG-ADM01', // Carlos Pérez
                'area' => 'Administración',
                'nivel_grado' => 'Licenciatura',
                'nivel_exp' => '8 años',
                'maestria' => 'No presenta',
                'doctorado' => 'No presenta',
                'diplomado' => 'Diplomado en Gestión Pública'
            ],
        ]);
    }
}
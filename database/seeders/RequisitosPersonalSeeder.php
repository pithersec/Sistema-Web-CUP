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
                'registro_personal' => '1003',
                'area'        => 'sistemas',
                'nivel_grado' => 'licenciatura',
                'nivel_exp'   => 15,
                'maestria'    => true,
                'doctorado'   => false,
                'diplomado'   => true,
            ],
            [
                'registro_personal' => '1001',
                'area'        => 'administracion',
                'nivel_grado' => 'licenciatura',
                'nivel_exp'   => 8,
                'maestria'    => false,
                'doctorado'   => false,
                'diplomado'   => true,
            ],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carrera')->insert([
            [
                'codigo' => '187-3',
                'plan' => 'PL-05',
                'nombre' => 'Ingeniería en Sistemas',
                'modalidad' => 'presencial',
                'nivel' => 'licenciatura',
                'tipo' => 'semestral',
                'duracion' => 10
            ],
            [
                'codigo' => '187-4',
                'plan' => 'PL-05',
                'nombre' => 'Ingeniería Informática',
                'modalidad' => 'presencial',
                'nivel' => 'licenciatura',
                'tipo' => 'semestral',
                'duracion' => 10
            ],
            [
                'codigo' => '187-5',
                'plan' => 'PL-08',
                'nombre' => 'Ingeniería en Redes y Telecomunicaciones',
                'modalidad' => 'presencial',
                'nivel' => 'licenciatura',
                'tipo' => 'semestral',
                'duracion' => 10
            ],
        ]);
    }
}
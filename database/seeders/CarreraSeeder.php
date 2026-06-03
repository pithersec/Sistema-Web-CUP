<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carrera')->insert([
            ['codigo' => '323', 'plan' => '0', 'nombre' => 'Ingeniería en Robótica',                  'modalidad' => 'presencial', 'nivel' => 'licenciatura', 'tipo' => 'semestral', 'duracion' => 8],
            ['codigo' => '187', 'plan' => '4', 'nombre' => 'Ingeniería en Sistemas',                  'modalidad' => 'presencial', 'nivel' => 'licenciatura', 'tipo' => 'semestral', 'duracion' => 10],
            ['codigo' => '187', 'plan' => '5', 'nombre' => 'Ingeniería en Redes y Telecomunicaciones','modalidad' => 'presencial', 'nivel' => 'licenciatura', 'tipo' => 'semestral', 'duracion' => 10],
            ['codigo' => '187', 'plan' => '6', 'nombre' => 'Ingeniería Informática',                  'modalidad' => 'presencial', 'nivel' => 'licenciatura', 'tipo' => 'semestral', 'duracion' => 9],
            ['codigo' => '187', 'plan' => '3', 'nombre' => 'Ingeniería Informática',                  'modalidad' => 'virtual',    'nivel' => 'licenciatura', 'tipo' => 'semestral', 'duracion' => 10],
            ['codigo' => '187', 'plan' => '4', 'nombre' => 'Ingeniería en Sistemas',                  'modalidad' => 'virtual',    'nivel' => 'licenciatura', 'tipo' => 'semestral', 'duracion' => 10],
        ]);
    }
}
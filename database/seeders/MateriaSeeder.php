<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materia')->insert([
            ['id' => 1, 'nombre' => 'Matemáticas', 'duracion' => '40 horas'],
            ['id' => 2, 'nombre' => 'Física', 'duracion' => '40 horas'],
            ['id' => 3, 'nombre' => 'Química', 'duracion' => '30 horas'],
        ]);
    }
}
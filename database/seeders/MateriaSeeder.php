<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materia')->insert([
            ['id' => 1, 'nombre' => 'Matemáticas', 'duracion' => 1.00],
            ['id' => 2, 'nombre' => 'Física',      'duracion' => 1.00],
            ['id' => 3, 'nombre' => 'Inglés',      'duracion' => 1.00],
            ['id' => 4, 'nombre' => 'Computación', 'duracion' => 1.00],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('personal')->insert([
            ['registro' => 'REG-ADM01', 'estado' => true, 'ci' => '5432100'], // Administrador
            ['registro' => 'REG-VEN02', 'estado' => true, 'ci' => '6543210'], // Ventanilla
            ['registro' => 'REG-DOC03', 'estado' => true, 'ci' => '7654321'], // Docente
        ]);
    }
}
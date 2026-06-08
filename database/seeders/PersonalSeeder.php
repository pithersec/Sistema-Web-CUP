<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('personal')->insert([
            ['registro' => '1001', 'estado' => true, 'ci' => '5432100'],
            ['registro' => '1002', 'estado' => true, 'ci' => '6543210'],
            ['registro' => '1003', 'estado' => true, 'ci' => '7654321'],
            ['registro' => '1004', 'estado' => true, 'ci' => '1234567'],
        ]);
    }
}
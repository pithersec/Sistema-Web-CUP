<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequisitosPostulanteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('requisitos_postulante')->insert([
            ['id' => 1, 'titulo_original' => true, 'titulo_copia' => true, 'fotocopia_carnet' => true, 'formulario' => true, 'comprobante' => true, 'libreta' => true],
            ['id' => 2, 'titulo_original' => false, 'titulo_copia' => true, 'fotocopia_carnet' => true, 'formulario' => true, 'comprobante' => true, 'libreta' => false],
            ['id' => 3, 'titulo_original' => true, 'titulo_copia' => true, 'fotocopia_carnet' => true, 'formulario' => true, 'comprobante' => true, 'libreta' => true],
        ]);
    }
}

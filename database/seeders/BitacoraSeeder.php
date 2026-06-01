<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BitacoraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bitacora')->insert([
            [
                'ip' => '127.0.0.1',
                'accion' => 'Inicio de sesión exitoso en el panel administrativo.',
                'fecha_hora' => '2026-02-01 08:00:00',
                'id_usuario' => 1 // Administrador
            ],
            [
                'ip' => '127.0.0.1',
                'accion' => 'Configuración masiva de cupos por carrera para la gestión 1-2026.',
                'fecha_hora' => '2026-02-01 09:15:30',
                'id_usuario' => 1
            ],
            [
                'ip' => '127.0.0.1',
                'accion' => 'Registro de preinscripción digital y validación de pago.',
                'fecha_hora' => '2026-02-10 14:22:12',
                'id_usuario' => 2 // Ventanilla
            ],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los grupos con su código de gestión para la asignación correcta
        $grupos = DB::table('grupo')->select('id', 'codigo_gestion')->get();

        // Asignación: docente => materia
        $asignaciones = [
            '1003' => 1, // Matemáticas
            '1002' => 2, // Física
            '1004' => 3, // Química
            '1001' => 4, // Computación
        ];

        $horarios = [
            'M' => ['hora_inicio' => '07:00', 'hora_fin' => '11:00'],
            'T' => ['hora_inicio' => '13:00', 'hora_fin' => '17:00'],
            'N' => ['hora_inicio' => '18:00', 'hora_fin' => '22:00'],
        ];

        $registros = [];
        $orden = 1;

        foreach ($asignaciones as $registro => $idMateria) {
            foreach ($grupos as $grupo) {
                $prefijo = substr($grupo->id, 0, 1);
                $horario = $horarios[$prefijo] ?? $horarios['M'];
                $registros[] = [
                    'id_materia'        => $idMateria,
                    'id_grupo'          => $grupo->id,
                    'gestion_grupo'     => $grupo->codigo_gestion,
                    'hora_inicio'       => $horario['hora_inicio'],
                    'hora_fin'          => $horario['hora_fin'],
                    'orden'             => $orden,
                    'registro_personal' => $registro,
                ];
            }
            $orden++;
        }

        DB::table('grupo_materia')->insert($registros);
    }
}
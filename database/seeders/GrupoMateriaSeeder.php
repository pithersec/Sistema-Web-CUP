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
            'REG-DOC03' => 1, // Matemáticas
            'REG-VEN02' => 2, // Física
            'REG-SIS04' => 3, // Química
            'REG-ADM01' => 4, // Computación
        ];

        $horarios = [
            'M' => '07:00 - 11:00',
            'T' => '13:00 - 17:00',
            'N' => '18:00 - 22:00',
        ];

        $registros = [];

        foreach ($asignaciones as $registro => $idMateria) {
            foreach ($grupos as $grupo) {
                $prefijo = substr($grupo->id, 0, 1);
                $registros[] = [
                    'id_materia'        => $idMateria,
                    'id_grupo'          => $grupo->id,
                    'gestion_grupo'     => $grupo->codigo_gestion, 
                    'horario'           => $horarios[$prefijo] ?? '07:00 - 11:00',
                    'registro_personal' => $registro,
                ];
            }
        }

        DB::table('grupo_materia')->insert($registros);
    }
}
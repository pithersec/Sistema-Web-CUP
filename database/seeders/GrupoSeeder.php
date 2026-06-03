<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $maxPorGrupo = 70;
        $turnos = ['mañana', 'tarde', 'noche'];
        $horarios = [
            'mañana' => '07:00 - 11:00',
            'tarde'  => '13:00 - 17:00',
            'noche'  => '18:00 - 22:00',
        ];
        $prefijos = ['mañana' => 'M', 'tarde' => 'T', 'noche' => 'N'];

        $gestiones = [
            ['codigo' => '1-2025', 'total' => 847],
            ['codigo' => '2-2025', 'total' => 1134],
            ['codigo' => '1-2026', 'total' => 923],
        ];

        $grupos = [];
        $contadores = ['M' => 1, 'T' => 1, 'N' => 1];

        foreach ($gestiones as $gestion) {
            $totalGrupos = (int) ceil($gestion['total'] / $maxPorGrupo);
            $turnoIndex = 0;

            for ($i = 0; $i < $totalGrupos; $i++) {
                $turno = $turnos[$turnoIndex % 3];
                $prefijo = $prefijos[$turno];
                $id = $prefijo . str_pad($contadores[$prefijo]++, 3, '0', STR_PAD_LEFT);

                $grupos[] = [
                    'id'             => $id,
                    'aula'           => 'AULA-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'turno'          => $turno,
                    'horario'        => $horarios[$turno],
                    'total_ins'      => 0,
                    'codigo_gestion' => $gestion['codigo'],
                ];

                $turnoIndex++;
            }
        }

        DB::table('grupo')->insert($grupos);
    }
}
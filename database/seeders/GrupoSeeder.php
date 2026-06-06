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
            ['codigo' => '2-2026', 'total' => 821],
        ];

        $grupos = [];

        $aulas = [
            '236-11', '236-12', '236-13', '236-14', '236-15',
            '236-21', '236-22', '236-23', '236-24', '236-25',
            '236-31', '236-32', '236-33', '236-34', '236-35',
        ];

        foreach ($gestiones as $gestion) {
            $contadores = ['M' => 1, 'T' => 1, 'N' => 1];
            $totalGrupos = (int) ceil($gestion['total'] / $maxPorGrupo);
            $turnoIndex = 0;

            for ($i = 0; $i < $totalGrupos; $i++) {
                $turno = $turnos[$turnoIndex % 3];
                $prefijo = $prefijos[$turno];
                $id = $prefijo . str_pad($contadores[$prefijo]++, 3, '0', STR_PAD_LEFT);

                $grupos[] = [   
                    'id'             => $id,
                    'aula'           => $aulas[$i % count($aulas)],
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
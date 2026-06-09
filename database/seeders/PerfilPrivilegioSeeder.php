<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilPrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        $privilegios = [];

        // SISTEMA (id: 1) — Acceso total: hereda todos los privilegios
        for ($i = 1; $i <= 28; $i++) {
            $privilegios[] = ['id_perfil' => 1, 'id_privilegio' => $i];
        }

        // ADMINISTRADOR (id: 2)
        $adminPrivilegios = [
            2, 3, 4,        // usuarios (ver, editar, cargar)
            5,              // perfiles.gestionar
            6, 7, 8,        // postulantes (ver, editar, validar)
            9, 10, 11, 12,  // docentes (ver, crear, editar, desactivar)
            13, 14,         // carreras.ver, cupos.editar
            15, 16,         // grupos (ver, asignar)
            17,             // materias.gestionar
            18, 19,         // gestiones (ver, gestionar)
            20,             // notas.ver
            23,             // bitacora.ver
            24,             // reportes.ver
            25,             // rendimiento.ver
            26,             // reclamos.gestionar
            28,             // configuracion.gestionar
        ];
        foreach ($adminPrivilegios as $pid) {
            $privilegios[] = ['id_perfil' => 2, 'id_privilegio' => $pid];
        }

        // DOCENTE (id: 3)
        $docentePrivilegios = [20, 21, 22, 25, 27]; // notas.ver, registrar, editar, rendimiento.ver, asistencia.registrar
        foreach ($docentePrivilegios as $pid) {
            $privilegios[] = ['id_perfil' => 3, 'id_privilegio' => $pid];
        }

        DB::table('perfil_privilegio')->insert($privilegios);
    }
}

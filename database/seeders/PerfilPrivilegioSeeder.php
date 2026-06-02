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

        // ADMINISTRADOR (id: 2) — Gestión académica, sin configuración del sistema
        $adminPrivilegios = [
            2, 3, 4, 5,       // usuarios (ver, crear, editar, eliminar)
            8, 9, 10, 11,     // postulantes (ver, aprobar, rechazar, validar)
            12, 13, 14, 15,   // docentes (ver, crear, editar, desactivar)
            16, 17,           // carreras.ver, cupos.editar
            18, 19,           // grupos (ver, crear)
            20, 21,           // materias (ver, gestionar)
            22, 23,           // gestiones (ver, gestionar)
            24,               // notas.ver
            28,               // reportes.ver
        ];
        foreach ($adminPrivilegios as $pid) {
            $privilegios[] = ['id_perfil' => 2, 'id_privilegio' => $pid];
        }

        // DOCENTE (id: 3) — Solo sus grupos y notas
        $docentePrivilegios = [24, 25, 26]; // notas.ver, notas.registrar, notas.editar
        foreach ($docentePrivilegios as $pid) {
            $privilegios[] = ['id_perfil' => 3, 'id_privilegio' => $pid];
        }

        DB::table('perfil_privilegio')->insert($privilegios);
    }
}

<?php

namespace App\Imports;

use App\Models\DatosPersonales;
use App\Models\Personal;
use App\Models\Usuario;
use App\Models\Bitacora;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PersonalImport implements ToCollection, WithHeadingRow
{
    public array $creados  = [];
    public array $errores  = [];
    public array $omitidos = [];

    private string $ip;
    private int $idUsuarioAdmin;

    public function __construct(string $ip, int $idUsuarioAdmin)
    {
        $this->ip             = $ip;
        $this->idUsuarioAdmin = $idUsuarioAdmin;
    }

    public function collection(Collection $rows): void
    {
        $perfilMap = ['Administrador' => 2, 'Docente' => 3];
        $generoMap = ['Masculino' => 'm', 'Femenino' => 'f'];

        $nextRegistro = (int) DB::table('personal')->max('registro') + 1;

        foreach ($rows as $index => $row) {
            $fila = $index + 2;

            $camposRequeridos = ['ci', 'nombre', 'apellido', 'genero', 'correo', 'fecha_nacimiento', 'direccion', 'perfil'];
            foreach ($camposRequeridos as $campo) {
                if (empty($row[$campo])) {
                    $this->errores[] = "Fila {$fila}: campo '{$campo}' vacío — omitido.";
                    continue 2;
                }
            }

            $ci     = trim((string) $row['ci']);
            $perfil = trim($row['perfil']);
            $genero = trim($row['genero']);

            if (!isset($perfilMap[$perfil])) {
                $this->errores[] = "Fila {$fila}: perfil '{$perfil}' no válido (use Administrador o Docente) — omitido.";
                continue;
            }

            if (!isset($generoMap[$genero])) {
                $this->errores[] = "Fila {$fila}: género '{$genero}' no válido (use Masculino o Femenino) — omitido.";
                continue;
            }

            try {
                $fechaNac = Carbon::createFromFormat('d/m/Y', trim($row['fecha_nacimiento']))->format('Y-m-d');
            } catch (\Exception $e) {
                $this->errores[] = "Fila {$fila}: fecha_nac '{$row['fecha_nacimiento']}' inválida (use DD/MM/YYYY) — omitido.";
                continue;
            }

            if (DatosPersonales::where('ci', $ci)->exists()) {
                $this->omitidos[] = "Fila {$fila}: CI {$ci} ya existe en el sistema — omitido.";
                continue;
            }

            $inicial  = strtolower(substr(trim($row['nombre']), 0, 1));
            $apellido = strtolower(preg_replace('/\s+/', '', trim($row['apellido'])));
            $sufijo   = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
            $userName = $inicial . $apellido . $sufijo;

            while (Usuario::where('user_name', $userName)->exists()) {
                $sufijo   = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
                $userName = $inicial . $apellido . $sufijo;
            }

            $correo   = trim($row['correo']);
            $registro = (string) $nextRegistro;

            // Verificar duplicado por correo en usuario
            if (\App\Models\Usuario::where('email', $correo)->exists()) {
                $this->omitidos[] = "Fila {$fila}: correo '{$correo}' ya existe en el sistema — omitido.";
                continue;
            }

            DB::transaction(function () use (
                $ci, $row, $generoMap, $genero, $fechaNac, $correo,
                $registro, $userName, $perfilMap, $perfil
            ) {
                DatosPersonales::create([
                    'ci'        => $ci,
                    'nombre'    => trim($row['nombre']),
                    'apellido'  => trim($row['apellido']),
                    'genero'    => $generoMap[$genero],
                    'telefono'  => !empty($row['telefono']) ? trim($row['telefono']) : null,
                    'correo'    => $correo,
                    'fecha_nac' => $fechaNac,
                    'direccion' => trim($row['direccion']),
                ]);

                Personal::create([
                    'registro' => $registro,
                    'ci'       => $ci,
                    'estado'   => true,
                ]);

                Usuario::create([
                    'user_name'         => $userName,
                    'clave'             => $ci,
                    'email'             => $correo,
                    'id_perfil'         => $perfilMap[$perfil],
                    'registro_personal' => $registro,
                ]);
            });

            $this->creados[] = [
                'registro'  => $registro,
                'nombre'    => trim($row['nombre']) . ' ' . trim($row['apellido']),
                'user_name' => $userName,
                'perfil'    => $perfil,
            ];

            $nextRegistro++;
        }

        if (!empty($this->creados)) {
            Bitacora::create([
                'ip'         => $this->ip,
                'accion'     => 'Carga masiva de cuentas: ' . count($this->creados) . ' cuenta(s) creada(s).',
                'fecha_hora' => now(),
                'id_usuario' => $this->idUsuarioAdmin,
            ]);
        }
    }
}
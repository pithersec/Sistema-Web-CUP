<?php

namespace App\Imports;

use App\Models\Personal;
use App\Models\RequisitosPersonal;
use App\Models\Bitacora;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RequisitosImport implements ToCollection, WithHeadingRow
{
    public array $creados  = [];
    public array $errores  = [];
    public array $omitidos = [];

    private string $ip;
    private int $idUsuarioAdmin;

    private array $areasValidas  = ['matematicas','fisica','computacion','ingles','administracion','sistemas','otra'];
    private array $gradosValidos = ['tecnico_medio','tecnico_superior','licenciatura','ingenieria','maestria','doctorado'];

    public function __construct(string $ip, int $idUsuarioAdmin)
    {
        $this->ip             = $ip;
        $this->idUsuarioAdmin = $idUsuarioAdmin;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $fila = $index + 2;

            $camposRequeridos = ['ci', 'area', 'nivel_grado', 'nivel_experiencia', 'maestria', 'doctorado', 'diplomado'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($row[$campo]) || $row[$campo] === '') {
                    $this->errores[] = "Fila {$fila}: campo '{$campo}' vacío — omitido.";
                    continue 2;
                }
            }

            $ci    = trim((string) $row['ci']);
            $area  = strtolower(trim($row['area']));
            $grado = strtolower(trim($row['nivel_grado']));

            $personal = Personal::whereHas('datosPersonales', fn($q) => $q->where('ci', $ci))->first();
            if (!$personal) {
                $this->errores[] = "Fila {$fila}: CI {$ci} no existe en el sistema — omitido.";
                continue;
            }

            if (!in_array($area, $this->areasValidas)) {
                $this->errores[] = "Fila {$fila}: área '{$area}' no válida — omitido.";
                continue;
            }

            if (!in_array($grado, $this->gradosValidos)) {
                $this->errores[] = "Fila {$fila}: nivel_grado '{$grado}' no válido — omitido.";
                continue;
            }

            $nivelExp = intval($row['nivel_experiencia']);
            if ($nivelExp < 0) {
                $this->errores[] = "Fila {$fila}: nivel_experiencia debe ser un número positivo — omitido.";
                continue;
            }

            $existe = RequisitosPersonal::where('registro_personal', $personal->registro)
                ->where('area', $area)->exists();
            if ($existe) {
                $this->omitidos[] = "Fila {$fila}: CI {$ci} ya tiene requisito para área '{$area}' — omitido.";
                continue;
            }

            $boolMap   = ['si' => true, 'no' => false, '1' => true, '0' => false];
            $maestria  = $boolMap[strtolower(trim($row['maestria']))]  ?? false;
            $doctorado = $boolMap[strtolower(trim($row['doctorado']))] ?? false;
            $diplomado = $boolMap[strtolower(trim($row['diplomado']))] ?? false;

            RequisitosPersonal::create([
                'registro_personal' => $personal->registro,
                'area'              => $area,
                'nivel_grado'       => $grado,
                'nivel_exp'         => $nivelExp,
                'maestria'          => $maestria,
                'doctorado'         => $doctorado,
                'diplomado'         => $diplomado,
            ]);

            $this->creados[] = [
                'ci'        => $ci,
                'registro'  => $personal->registro,
                'area'      => $area,
                'grado'     => $grado,
                'maestria'  => $maestria,
                'doctorado' => $doctorado,
                'diplomado' => $diplomado,
            ];
        }

        if (!empty($this->creados)) {
            Bitacora::create([
                'ip'         => $this->ip,
                'accion'     => 'Carga masiva de requisitos: ' . count($this->creados) . ' registro(s) creado(s).',
                'fecha_hora' => now(),
                'id_usuario' => $this->idUsuarioAdmin,
            ]);
        }
    }
}
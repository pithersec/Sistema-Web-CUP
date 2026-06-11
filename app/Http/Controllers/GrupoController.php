<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\GrupoMateria;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Personal;
use App\Models\Postulante;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    private array $materiaAreaMap = [
        'Matemáticas' => 'matematicas',
        'Física'      => 'fisica',
        'Inglés'      => 'ingles',
        'Computación' => 'computacion',
    ];

    private array $turnoPrefix = [
        'mañana' => 'M',
        'tarde'  => 'T',
        'noche'  => 'N',
    ];

    private function getInscritos()
    {
        return Postulante::where('estado', 'inscrito')->whereNull('id_grupo');
    }

    /**
     * CU-11: mostrarAsignacion()
     */
    public function mostrarAsignacion(Request $request)
    {
        $gestiones     = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
        $codigoGestion = $request->input('gestion', $gestiones->first()?->codigo);
        $gestion       = Gestion::find($codigoGestion);

        $inscritosPorTurno = [];
        $totalInscritos    = 0;

        if ($gestion) {
            $turnos = DB::table('turno')->orderByRaw("CASE WHEN nombre='mañana' THEN 0 WHEN nombre='tarde' THEN 1 ELSE 2 END")->get();
            foreach ($turnos as $turno) {
                $count = $this->getInscritos()->where('nombre_turno', $turno->nombre)->count();
                $inscritosPorTurno[$turno->nombre] = $count;
                $totalInscritos += $count;
            }
            $sinTurno = $this->getInscritos()->whereNull('nombre_turno')->count();
            if ($sinTurno > 0) {
                $inscritosPorTurno['sin turno'] = $sinTurno;
                $totalInscritos += $sinTurno;
            }
        }

        $grupos = Grupo::with(['grupoMaterias.materia', 'grupoMaterias.personal.datosPersonales'])
            ->where('codigo_gestion', $codigoGestion)
            ->orderByRaw("CASE WHEN nombre_turno='mañana' THEN 0 WHEN nombre_turno='tarde' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->get();

        $gruposGenerados = $grupos->isNotEmpty();

        $numGrupos    = $totalInscritos > 0 ? (int) ceil($totalInscritos / 70) : 0;
        $porTurno     = (int) floor($numGrupos / 3);
        $excedente    = $numGrupos % 3;
        $distribucion = ['mañana' => $porTurno + $excedente, 'tarde' => $porTurno, 'noche' => $porTurno];

        return view('grupos.index', compact(
            'gestiones', 'gestion', 'codigoGestion',
            'inscritosPorTurno', 'totalInscritos',
            'grupos', 'gruposGenerados',
            'numGrupos', 'distribucion'
        ));
    }

    /**
     * CU-11: generarGrupos()
     */
    public function generarGrupos(Request $request)
    {
        $codigoGestion = $request->input('gestion');
        $gestion       = Gestion::findOrFail($codigoGestion);

        if (Grupo::where('codigo_gestion', $codigoGestion)->exists()) {
            return redirect()->route('grupos.index', ['gestion' => $codigoGestion])
                ->with('error', 'Ya existen grupos generados para esta gestión.');
        }

        $totalInscritos = $this->getInscritos()->count();

        if ($totalInscritos === 0) {
            return redirect()->route('grupos.index', ['gestion' => $codigoGestion])
                ->with('error', 'No existen postulantes inscritos para esta gestión.');
        }

        $numGrupos = (int) ceil($totalInscritos / 70);
        $porTurno  = (int) floor($numGrupos / 3);
        $excedente = $numGrupos % 3;

        $turnos   = DB::table('turno')->orderByRaw("CASE WHEN nombre='mañana' THEN 0 WHEN nombre='tarde' THEN 1 ELSE 2 END")->get();
        $materias = Materia::all();

        $gruposPorTurno_count = [
            'mañana' => $porTurno + $excedente,
            'tarde'  => $porTurno,
            'noche'  => $porTurno,
        ];

        DB::transaction(function () use ($codigoGestion, $turnos, $materias, $gruposPorTurno_count, $totalInscritos) {
            $numMaterias    = $materias->count();
            $gruposPorTurno = [];

            foreach ($turnos as $turno) {
                $prefix   = $this->turnoPrefix[$turno->nombre] ?? strtoupper(substr($turno->nombre, 0, 1));
                $cantidad = $gruposPorTurno_count[$turno->nombre] ?? 0;
                $gruposPorTurno[$turno->nombre] = [];

                for ($i = 1; $i <= $cantidad; $i++) {
                    $grupoId = $prefix . str_pad($i, 3, '0', STR_PAD_LEFT);

                    Grupo::create([
                        'id'             => $grupoId,
                        'codigo_gestion' => $codigoGestion,
                        'nombre_turno'   => $turno->nombre,
                        'total_ins'      => 0,
                        'aula'           => null,
                    ]);

                    $offset = ($i - 1) % $numMaterias;
                    foreach (range(0, $numMaterias - 1) as $pos) {
                        $materia = $materias->values()[($offset + $pos) % $numMaterias];
                        GrupoMateria::create([
                            'id_grupo'          => $grupoId,
                            'gestion_grupo'     => $codigoGestion,
                            'id_materia'        => $materia->id,
                            'hora_inicio'       => null,
                            'hora_fin'          => null,
                            'orden'             => $pos + 1,
                            'registro_personal' => null,
                        ]);
                    }

                    $gruposPorTurno[$turno->nombre][] = $grupoId;
                }
            }

            $postulantes   = $this->getInscritos()->get();
            $contadorGrupo = collect($gruposPorTurno)->flatten()->mapWithKeys(fn($id) => [$id => 0])->toArray();
            $turnosOrden   = array_keys($gruposPorTurno);

            foreach ($postulantes as $postulante) {
                $turnoPreferido = $postulante->nombre_turno;
                $turnosIntento  = $turnoPreferido
                    ? array_unique(array_merge([$turnoPreferido], array_filter($turnosOrden, fn($t) => $t !== $turnoPreferido)))
                    : $turnosOrden;

                foreach ($turnosIntento as $turno) {
                    if (empty($gruposPorTurno[$turno])) continue;
                    $asignado = false;
                    foreach ($gruposPorTurno[$turno] as $grupoId) {
                        if ($contadorGrupo[$grupoId] < 70) {
                            $postulante->update(['id_grupo' => $grupoId, 'gestion_grupo' => $codigoGestion]);
                            $contadorGrupo[$grupoId]++;
                            $asignado = true;
                            break;
                        }
                    }
                    if ($asignado) break;
                }
            }

            foreach ($contadorGrupo as $grupoId => $total) {
                Grupo::where('id', $grupoId)->where('codigo_gestion', $codigoGestion)->update(['total_ins' => $total]);
            }

            Bitacora::create([
                'ip'         => request()->ip(),
                'accion'     => "Generación de grupos para gestión {$codigoGestion}: {$totalInscritos} postulantes distribuidos.",
                'fecha_hora' => now(),
                'id_usuario' => Auth::id(),
            ]);
        });

        return redirect()->route('grupos.index', ['gestion' => $codigoGestion])
            ->with('success', "Grupos generados correctamente para la gestión {$codigoGestion}.");
    }

    /**
     * CU-11: mostrarFormAsignarDocente()
     */
    public function mostrarFormAsignarDocente(Request $request)
    {
        $grupoId       = $request->input('grupo');
        $codigoGestion = $request->input('gestion');
        $idMateria     = $request->input('materia');

        $grupoMateria = GrupoMateria::with(['grupo', 'materia'])
            ->where('id_grupo', $grupoId)
            ->where('gestion_grupo', $codigoGestion)
            ->where('id_materia', $idMateria)
            ->firstOrFail();

        $nombreMateria = $grupoMateria->materia->nombre;
        $areaNecesaria = $this->materiaAreaMap[$nombreMateria] ?? null;
        $turnoGrupo    = $grupoMateria->grupo->nombre_turno;

        $docentes = Personal::with(['datosPersonales', 'requisitosPersonal'])
            ->where('estado', true)
            ->whereHas('requisitosPersonal', fn($q) => $q->where('area', $areaNecesaria))
            ->whereHas('usuario', fn($q) => $q->where('id_perfil', 3))
            ->get()
            ->map(function ($p) use ($idMateria, $turnoGrupo, $codigoGestion) {
                $totalAsignados = GrupoMateria::where('registro_personal', $p->registro)
                    ->where('gestion_grupo', $codigoGestion)->count();

                $cruceMateria = GrupoMateria::where('registro_personal', $p->registro)
                    ->where('gestion_grupo', $codigoGestion)
                    ->where('id_materia', $idMateria)
                    ->whereHas('grupo', fn($q) => $q->where('nombre_turno', $turnoGrupo))
                    ->exists();

                $p->total_asignados = $totalAsignados;
                $p->cruce_materia   = $cruceMateria;
                $p->disponible      = $totalAsignados < 4 && !$cruceMateria;
                return $p;
            });

        return view('grupos.asignar-docente', compact(
            'grupoMateria', 'docentes', 'areaNecesaria',
            'grupoId', 'codigoGestion', 'idMateria'
        ));
    }

    /**
     * CU-11: asignarDocente()
     */
    public function asignarDocente(Request $request)
    {
        $grupoId          = $request->input('grupo');
        $codigoGestion    = $request->input('gestion');
        $idMateria        = $request->input('materia');
        $registroPersonal = $request->input('registro_personal');

        $grupoMateria = GrupoMateria::with(['grupo', 'materia'])
            ->where('id_grupo', $grupoId)
            ->where('gestion_grupo', $codigoGestion)
            ->where('id_materia', $idMateria)
            ->firstOrFail();

        $turnoGrupo = $grupoMateria->grupo->nombre_turno;

        $totalAsignados = GrupoMateria::where('registro_personal', $registroPersonal)
            ->where('gestion_grupo', $codigoGestion)->count();

        if ($totalAsignados >= 4) {
            return back()->with('error', 'El docente ya tiene 4 grupos asignados en esta gestión.');
        }

        $cruce = GrupoMateria::where('registro_personal', $registroPersonal)
            ->where('gestion_grupo', $codigoGestion)
            ->where('id_materia', $idMateria)
            ->whereHas('grupo', fn($q) => $q->where('nombre_turno', $turnoGrupo))
            ->exists();

        if ($cruce) {
            return back()->with('error', 'El docente ya dicta esta materia en otro grupo del mismo turno.');
        }

        $grupoMateria->update(['registro_personal' => $registroPersonal]);

        $docente = Personal::with('datosPersonales')->find($registroPersonal);
        Bitacora::create([
            'ip'         => $request->ip(),
            'accion'     => "Asignación docente {$docente->datosPersonales->nombre} {$docente->datosPersonales->apellido} → grupo {$grupoId}, materia {$grupoMateria->materia->nombre}, gestión {$codigoGestion}.",
            'fecha_hora' => now(),
            'id_usuario' => Auth::id(),
        ]);

        return redirect()->route('grupos.index', ['gestion' => $codigoGestion])
            ->with('success', 'Docente asignado correctamente.');
    }
}
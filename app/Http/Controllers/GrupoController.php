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
use Carbon\Carbon;

class GrupoController extends Controller
{
    // Mapeo materia → área de requisitos_personal
    private array $materiaAreaMap = [
        'Matemáticas' => 'matematicas',
        'Física'      => 'fisica',
        'Inglés'      => 'ingles',
        'Computación' => 'computacion',
    ];

    // Prefijo de ID de grupo por turno
    private array $turnoPrefix = [
        'mañana' => 'M',
        'tarde'  => 'T',
        'noche'  => 'N',
    ];

    /**
     * Postulantes inscritos sin grupo asignado (candidatos para CU-11)
     */
    private function getInscritos()
    {
        return Postulante::where('estado', 'inscrito')->whereNull('id_grupo');
    }

    /**
     * CU-11: mostrarAsignacion()
     * Vista principal — muestra inscritos por turno, cálculo de grupos y tabla de grupos existentes
     */
    public function mostrarAsignacion(Request $request)
    {
        $gestiones     = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
        $codigoGestion = $request->input('gestion', $gestiones->first()?->codigo);
        $gestion       = Gestion::find($codigoGestion);

        $inscritosPorTurno = [];
        $totalInscritos    = 0;

        if ($gestion) {
            $turnos       = DB::table('turno')->orderByRaw("CASE WHEN nombre='mañana' THEN 0 WHEN nombre='tarde' THEN 1 ELSE 2 END")->get();
            $gestionCorta = str_replace('-', '', $codigoGestion);

            // Contar inscritos sin grupo por turno, filtrando por gestión via prefijo de código
            foreach ($turnos as $turno) {
                $count = Postulante::where('estado', 'inscrito')
                    ->whereNull('id_grupo')
                    ->where('nombre_turno', $turno->nombre)
                    ->where('codigo', 'LIKE', $gestionCorta . '%')
                    ->count();
                $inscritosPorTurno[$turno->nombre] = $count;
                $totalInscritos += $count;
            }
            $sinTurno = Postulante::where('estado', 'inscrito')
                ->whereNull('id_grupo')
                ->whereNull('nombre_turno')
                ->where('codigo', 'LIKE', $gestionCorta . '%')
                ->count();
            if ($sinTurno > 0) {
                $inscritosPorTurno['sin turno'] = $sinTurno;
                $totalInscritos += $sinTurno;
            }
        }

        // Cargar grupos sin eager loading (Grupo tiene PK compuesta, rompe el with())
        $grupos = Grupo::where('codigo_gestion', $codigoGestion)
            ->orderByRaw("CASE WHEN nombre_turno='mañana' THEN 0 WHEN nombre_turno='tarde' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->get();

        $grupoIds = $grupos->pluck('id');

        // Cargar grupo_materia con nombre de materia directamente via query
        $grupoMaterias = DB::table('grupo_materia')
            ->join('materia', 'grupo_materia.id_materia', '=', 'materia.id')
            ->whereIn('grupo_materia.id_grupo', $grupoIds)
            ->where('grupo_materia.gestion_grupo', $codigoGestion)
            ->select(
                'grupo_materia.id_grupo',
                'grupo_materia.id_materia',
                'grupo_materia.orden',
                'grupo_materia.registro_personal',
                'grupo_materia.hora_inicio',
                'grupo_materia.hora_fin',
                'materia.nombre as materia_nombre'
            )
            ->orderBy('grupo_materia.orden')
            ->get()
            ->groupBy('id_grupo');

        // Cargar datos de docentes asignados en un solo query
        $registrosPersonal = DB::table('grupo_materia')
            ->whereIn('id_grupo', $grupoIds)
            ->where('gestion_grupo', $codigoGestion)
            ->whereNotNull('registro_personal')
            ->pluck('registro_personal')
            ->unique();

        $docentesMap = DB::table('personal')
            ->join('datos_personales', 'personal.ci', '=', 'datos_personales.ci')
            ->whereIn('personal.registro', $registrosPersonal)
            ->select('personal.registro', 'datos_personales.nombre', 'datos_personales.apellido')
            ->get()
            ->keyBy(fn($d) => (string) $d->registro);

        $gruposGenerados = $grupos->isNotEmpty();

        // Calcular distribución para mostrar en la tarjeta de cálculo
        $numGrupos    = $totalInscritos > 0 ? (int) ceil($totalInscritos / 70) : 0;
        $porTurno     = (int) floor($numGrupos / 3);
        $excedente    = $numGrupos % 3;
        $distribucion = ['mañana' => $porTurno + $excedente, 'tarde' => $porTurno, 'noche' => $porTurno];

        return view('grupos.index', compact(
            'gestiones', 'gestion', 'codigoGestion',
            'inscritosPorTurno', 'totalInscritos',
            'grupos', 'gruposGenerados', 'grupoMaterias',
            'numGrupos', 'distribucion', 'docentesMap'
        ));
    }

    /**
     * CU-11: generarGrupos()
     * Crea grupos por turno, asigna materias con rotación y calcula horarios automáticamente
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

        // Calcular grupos totales y distribución por turno
        // Excedente siempre va a mañana
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

            // Precargar hora_inicio de cada turno para calcular horarios
            $turnosData = DB::table('turno')->pluck('hora_inicio', 'nombre');

            foreach ($turnos as $turno) {
                $prefix          = $this->turnoPrefix[$turno->nombre] ?? strtoupper(substr($turno->nombre, 0, 1));
                $cantidad        = $gruposPorTurno_count[$turno->nombre] ?? 0;
                $turnoHoraInicio = Carbon::createFromFormat('H:i:s', $turnosData[$turno->nombre]);
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

                    // Rotar materias entre grupos del mismo turno
                    // Cada grupo empieza en un offset distinto: G1→Mat1, G2→Mat2, etc.
                    $offset    = ($i - 1) % $numMaterias;
                    $acumulado = 0; // horas acumuladas desde inicio del turno

                    foreach (range(0, $numMaterias - 1) as $pos) {
                        $materia    = $materias->values()[($offset + $pos) % $numMaterias];
                        $duracion   = (float) $materia->duracion;

                        // Calcular hora_inicio y hora_fin según acumulado de duraciones
                        $horaInicio = (clone $turnoHoraInicio)->addMinutes((int)($acumulado * 60));
                        $horaFin    = (clone $horaInicio)->addMinutes((int)($duracion * 60));
                        $acumulado += $duracion;

                        GrupoMateria::create([
                            'id_grupo'          => $grupoId,
                            'gestion_grupo'     => $codigoGestion,
                            'id_materia'        => $materia->id,
                            'hora_inicio'       => $horaInicio->format('H:i:s'),
                            'hora_fin'          => $horaFin->format('H:i:s'),
                            'orden'             => $pos + 1,
                            'registro_personal' => null,
                        ]);
                    }

                    $gruposPorTurno[$turno->nombre][] = $grupoId;
                }
            }

            // Distribuir postulantes respetando turno preferido
            // Si el turno está lleno, se reasigna al siguiente turno disponible
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

            // Actualizar total_ins en cada grupo
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
     * Muestra docentes disponibles para un grupo+materia
     * Filtra por área coincidente, límite de 4 grupos y cruce de horario
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

        $docentes = Personal::with(['datosPersonales', 'requisitosPersonal'])
            ->where('estado', true)
            ->whereHas('requisitosPersonal', fn($q) => $q->where('area', $areaNecesaria))
            ->whereHas('usuario', fn($q) => $q->where('id_perfil', 3))
            ->get()
            ->map(function ($p) use ($idMateria, $codigoGestion, $grupoId, $grupoMateria) {
                // Contar cuántos grupo_materia tiene asignados en esta gestión
                $totalAsignados = GrupoMateria::where('registro_personal', $p->registro)
                    ->where('gestion_grupo', $codigoGestion)->count();

                // Verificar cruce de horario: solapamiento de intervalos
                // Un docente tiene cruce si ya tiene asignada una materia que se superpone
                // con el horario de la materia que se quiere asignar
                $cruceMateria = GrupoMateria::where('registro_personal', $p->registro)
                    ->where('gestion_grupo', $codigoGestion)
                    ->where('hora_inicio', '<', $grupoMateria->hora_fin)
                    ->where('hora_fin', '>', $grupoMateria->hora_inicio)
                    ->where(fn($q) => $q->where('id_grupo', '!=', $grupoId)
                        ->orWhere('id_materia', '!=', $idMateria))
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
     * Guarda la asignación validando límite de 4 grupos y cruce de horario
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

        // Validar límite de 4 grupos por docente por gestión
        $totalAsignados = GrupoMateria::where('registro_personal', $registroPersonal)
            ->where('gestion_grupo', $codigoGestion)->count();

        if ($totalAsignados >= 4) {
            return back()->with('error', 'El docente ya tiene 4 grupos asignados en esta gestión.');
        }

        // Validar cruce de horario por solapamiento de intervalos
        $cruce = GrupoMateria::where('registro_personal', $registroPersonal)
            ->where('gestion_grupo', $codigoGestion)
            ->where('hora_inicio', '<', $grupoMateria->hora_fin)
            ->where('hora_fin', '>', $grupoMateria->hora_inicio)
            ->where(fn($q) => $q->where('id_grupo', '!=', $grupoId)
                ->orWhere('id_materia', '!=', $idMateria))
            ->exists();

        if ($cruce) {
            return back()->with('error', 'El docente tiene un cruce de horario con otra asignación.');
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
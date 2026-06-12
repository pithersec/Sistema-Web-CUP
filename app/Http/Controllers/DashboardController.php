<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perfil = strtolower($user->perfil->nombre);

        if ($perfil === 'sistema' || $perfil === 'administrador') {
            return $this->dashboardAdmin($request);
        }

        return $this->dashboardDocente($request);
    }

    private function dashboardAdmin(Request $request)
    {
        try {
            $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();

            $gestionCodigo = $request->get('gestion', $gestiones->first()?->codigo);
            $gestionActual = Gestion::find($gestionCodigo);

            $idsGrupos = Grupo::where('codigo_gestion', $gestionCodigo)->pluck('id');

            $postulantes = DB::table('postulante')
                ->where(function($q) use ($gestionCodigo) {
                    $gestionCorta = str_replace('-', '', $gestionCodigo);
                    $q->where('gestion_grupo', $gestionCodigo)
                    ->orWhere(function($q2) use ($gestionCorta) {
                        $q2->whereNull('gestion_grupo')
                            ->where('codigo', 'LIKE', $gestionCorta . '%');
                    });
                })
                ->select('codigo', 'estado', 'id_pago')
                ->get();

            $totalInscritos  = $postulantes->whereIn('estado', ['inscrito', 'aprobado', 'reprobado'])->count()
                            + $postulantes->where('estado', 'baja')->whereNotNull('id_pago')->count();
            $totalAprobados  = $postulantes->where('estado', 'aprobado')->count();
            $totalReprobados = $postulantes->where('estado', 'reprobado')->count();
            $totalGrupos     = $idsGrupos->count();

            $cuposTotales = DB::table('carrera_gestion')
                ->where('codigo_gestion', $gestionCodigo)
                ->sum('cupos');

            $tasaAprobacion = $totalInscritos > 0
                ? round(($totalAprobados / $totalInscritos) * 100)
                : 0;

            $kpis = [
                'total_inscritos'    => $totalInscritos,
                'total_aprobados'    => $totalAprobados,
                'total_reprobados'   => $totalReprobados,
                'grupos_habilitados' => $totalGrupos,
                'cupos_totales'      => $cuposTotales,
                'tasa_aprobacion'    => $tasaAprobacion,
            ];

            return view('dashboard', compact('kpis', 'gestionActual', 'gestiones', 'gestionCodigo'));

        } catch (\Exception $e) {
            Log::error('Error en Dashboard Admin: ' . $e->getMessage());
            return view('dashboard', [
                'kpis'          => ['total_inscritos' => 0, 'total_aprobados' => 0, 'total_reprobados' => 0, 'grupos_habilitados' => 0, 'cupos_totales' => 0, 'tasa_aprobacion' => 0],
                'gestionActual' => null,
                'gestiones'     => collect([]),
                'gestionCodigo' => null,
            ]);
        }
    }

    private function dashboardDocente(Request $request)
    {
        try {
            $user = Auth::user();
            $personal = $user->personal;

            $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
            $gestionCodigo = $request->get('gestion', $gestiones->first()?->codigo);

            if (!$personal) {
                return view('dashboard_docente', [
                    'grupos'       => collect([]),
                    'gestiones'    => $gestiones,
                    'gestionCodigo'=> $gestionCodigo,
                    'nombreDocente'=> $user->user_name,
                ]);
            }

            // Grupos del docente en esta gestión
            $grupoIds = DB::table('grupo_materia')
                ->where('registro_personal', $personal->registro)
                ->where('gestion_grupo', $gestionCodigo)
                ->distinct()
                ->pluck('id_grupo');

            $grupos = DB::table('grupo')
                ->whereIn('id', $grupoIds)
                ->where('codigo_gestion', $gestionCodigo)
                ->orderByRaw("CASE WHEN nombre_turno = 'mañana' THEN 0 WHEN nombre_turno = 'tarde' THEN 1 ELSE 2 END")
                ->orderBy('id')
                ->get()
                ->map(function($grupo) use ($personal, $gestionCodigo) {

                    // Materias del docente en este grupo
                    $materias = DB::table('grupo_materia')
                        ->join('materia', 'grupo_materia.id_materia', '=', 'materia.id')
                        ->where('grupo_materia.id_grupo', $grupo->id)
                        ->where('grupo_materia.gestion_grupo', $gestionCodigo)
                        ->where('grupo_materia.registro_personal', $personal->registro)
                        ->select('materia.id', 'materia.nombre')
                        ->get();

                    // Total postulantes en el grupo
                    $totalPostulantes = DB::table('postulante')
                        ->where('id_grupo', $grupo->id)
                        ->where('gestion_grupo', $gestionCodigo)
                        ->count();

                    $codigosPostulantes = DB::table('postulante')
                        ->where('id_grupo', $grupo->id)
                        ->where('gestion_grupo', $gestionCodigo)
                        ->pluck('codigo');

                    // Notas y stats por materia
                    $materiasConStats = $materias->map(function($materia) use ($codigosPostulantes, $totalPostulantes) {
                        $notas = DB::table('examen')
                            ->whereIn('codigo_postulante', $codigosPostulantes)
                            ->where('id_materia', $materia->id)
                            ->get();

                        $registradas = $notas->count();
                        $aprobados   = $notas->where('nota', '>=', 60)->count();
                        $tasa        = $registradas > 0 ? round(($aprobados / $registradas) * 100) : 0;
                        $pendientes  = max(0, ($totalPostulantes * 3) - $registradas); // 3 examenes por materia

                        return [
                            'id'          => $materia->id,
                            'nombre'      => $materia->nombre,
                            'registradas' => $registradas,
                            'pendientes'  => $pendientes,
                            'aprobados'   => $aprobados,
                            'tasa'        => $tasa,
                        ];
                    });

                    return [
                        'id'               => $grupo->id,
                        'nombre_turno'     => $grupo->nombre_turno,
                        'aula'             => $grupo->aula,
                        'total_postulantes'=> $totalPostulantes,
                        'materias'         => $materiasConStats,
                    ];
                });

            $nombreDocente = $personal->datosPersonales->nombre ?? $user->user_name;

            return view('dashboard_docente', compact('grupos', 'gestiones', 'gestionCodigo', 'nombreDocente'));

        } catch (\Exception $e) {
            Log::error('Error en Dashboard Docente: ' . $e->getMessage());
            return view('dashboard_docente', [
                'grupos'        => collect([]),
                'gestiones'     => collect([]),
                'gestionCodigo' => null,
                'nombreDocente' => Auth::user()->user_name,
            ]);
        }
    }
}
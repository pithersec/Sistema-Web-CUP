<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RendimientoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perfil = strtolower($user->perfil->nombre);
        $esDocente = $perfil === 'docente';

        $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
        $gestionCodigo = $request->get('gestion', $gestiones->first()?->codigo);

        // Grupos disponibles según perfil
        if ($esDocente) {
            $personal = $user->personal;
            $grupos = DB::table('grupo_materia')
                ->join('grupo', function($join) {
                    $join->on('grupo_materia.id_grupo', '=', 'grupo.id')
                        ->on('grupo_materia.gestion_grupo', '=', 'grupo.codigo_gestion');
                })
                ->where('grupo_materia.registro_personal', $personal->registro)
                ->where('grupo_materia.gestion_grupo', $gestionCodigo)
                ->select('grupo.id', 'grupo.nombre_turno', 'grupo.aula', 'grupo.codigo_gestion')
                ->groupBy('grupo.id', 'grupo.nombre_turno', 'grupo.aula', 'grupo.codigo_gestion')
                ->orderByRaw("CASE WHEN grupo.nombre_turno = 'mañana' THEN 0 WHEN grupo.nombre_turno = 'tarde' THEN 1 ELSE 2 END")
                ->get();
        } else {
            $grupos = DB::table('grupo')
                ->where('codigo_gestion', $gestionCodigo)
                ->orderByRaw("CASE WHEN nombre_turno = 'mañana' THEN 0 WHEN nombre_turno = 'tarde' THEN 1 ELSE 2 END")
                ->get();
        }

        // Materias disponibles según perfil
        if ($esDocente) {
            $materias = DB::table('grupo_materia')
                ->join('materia', 'grupo_materia.id_materia', '=', 'materia.id')
                ->where('grupo_materia.registro_personal', $personal->registro)
                ->where('grupo_materia.gestion_grupo', $gestionCodigo)
                ->select('materia.id', 'materia.nombre')
                ->distinct()
                ->orderBy('materia.nombre')
                ->get();
        } else {
            $materias = DB::table('materia')->orderBy('nombre')->get();
        }

        $carreras = Carrera::orderByRaw("CASE WHEN modalidad = 'presencial' THEN 0 ELSE 1 END")
            ->orderBy('nombre')->get();

        // Filtros
        $idGrupo   = $request->get('id_grupo');
        $idMateria = $request->get('id_materia');
        $carrera   = $request->get('carrera');

        // Query principal
        $query = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->where('postulante.gestion_grupo', $gestionCodigo)
            ->whereNotNull('postulante.id_grupo');

        if ($esDocente) {
            $grupoIds = $grupos->pluck('id');
            $query->whereIn('postulante.id_grupo', $grupoIds);
        }

        if ($idGrupo) {
            $query->where('postulante.id_grupo', $idGrupo);
        }

        if ($carrera && $carrera !== 'Todas') {
            [$codigoC, $planC, $modalidadC] = explode('|', $carrera);
            $query->whereExists(function($q) use ($codigoC, $planC, $modalidadC) {
                $q->select(DB::raw(1))
                    ->from('postulante_carrera')
                    ->whereColumn('postulante_carrera.codigo_postulante', 'postulante.codigo')
                    ->where('postulante_carrera.codigo_carrera', $codigoC)
                    ->where('postulante_carrera.plan_carrera', $planC)
                    ->where('postulante_carrera.modalidad_carrera', $modalidadC)
                    ->where('postulante_carrera.opcion', 1);
            });
        }

        $postulantes = $query->select(
            'postulante.codigo',
            'postulante.ci',
            'postulante.estado',
            'postulante.id_grupo',
            'datos_personales.nombre',
            'datos_personales.apellido'
        )->orderBy('postulante.codigo', 'asc')->paginate(15);

        // Para cada postulante obtener sus notas por materia
        $materiasDisponibles = $idMateria
            ? $materias->where('id', $idMateria)
            : $materias;

        $items = $postulantes->getCollection()->map(function($p) use ($materiasDisponibles) {
            $notasPorMateria = [];
            $sumaFinal = 0;
            $countMaterias = 0;

            foreach ($materiasDisponibles as $m) {
                $examenes = DB::table('examen')
                    ->where('codigo_postulante', $p->codigo)
                    ->where('id_materia', $m->id)
                    ->get();

                $e1 = $examenes->firstWhere('nro_examen', 1);
                $e2 = $examenes->firstWhere('nro_examen', 2);
                $e3 = $examenes->firstWhere('nro_examen', 3);

                $notaFinal = round(
                    (($e1->nota ?? 0) * (($e1->ponderacion ?? 30) / 100)) +
                    (($e2->nota ?? 0) * (($e2->ponderacion ?? 30) / 100)) +
                    (($e3->nota ?? 0) * (($e3->ponderacion ?? 40) / 100)),
                    1
                );

                $tieneNotas = $e1 || $e2 || $e3;
                $notasPorMateria[$m->id] = [
                    'nombre'    => $m->nombre,
                    'notaFinal' => $tieneNotas ? $notaFinal : null,
                    'aprobado'  => $tieneNotas ? $notaFinal >= 60 : null,
                ];

                if ($tieneNotas) {
                    $sumaFinal += $notaFinal;
                    $countMaterias++;
                }
            }

            $promedio = $countMaterias > 0 ? round($sumaFinal / $countMaterias, 1) : null;

            return [
                'codigo'          => $p->codigo,
                'ci'              => $p->ci,
                'nombre'          => $p->nombre . ' ' . $p->apellido,
                'estado'          => $p->estado,
                'id_grupo'        => $p->id_grupo,
                'notasPorMateria' => $notasPorMateria,
                'promedio'        => $promedio,
            ];
        });
        $postulantes->setCollection($items);
        $resultado = $postulantes;

        if ($resultado->isEmpty()) {
            $resultado = collect([]);
        }

        return view('rendimiento.index', compact(
            'resultado', 'gestiones', 'gestionCodigo',
            'grupos', 'materias', 'carreras', 'materiasDisponibles',
            'idGrupo', 'idMateria', 'carrera', 'esDocente'
        ));
    }

    public function detalle(Request $request, $codigo)
    {
        $user = Auth::user();
        $perfil = strtolower($user->perfil->nombre);
        $esDocente = $perfil === 'docente';

        $postulante = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->where('postulante.codigo', $codigo)
            ->select(
                'postulante.codigo',
                'postulante.ci',
                'postulante.estado',
                'postulante.id_grupo',
                'postulante.gestion_grupo',
                'datos_personales.nombre',
                'datos_personales.apellido'
            )->first();

        if (!$postulante) {
            return redirect()->route('rendimiento.index')->with('error', 'Postulante no encontrado.');
        }

        // Si es docente, verificar que el postulante pertenece a sus grupos
        if ($esDocente) {
            $personal = $user->personal;
            $grupoIds = DB::table('grupo_materia')
                ->where('registro_personal', $personal->registro)
                ->where('gestion_grupo', $postulante->gestion_grupo)
                ->pluck('id_grupo');

            if (!$grupoIds->contains($postulante->id_grupo)) {
                abort(403);
            }

            // Solo materias del docente
            $materias = DB::table('grupo_materia')
                ->join('materia', 'grupo_materia.id_materia', '=', 'materia.id')
                ->where('grupo_materia.registro_personal', $personal->registro)
                ->where('grupo_materia.gestion_grupo', $postulante->gestion_grupo)
                ->where('grupo_materia.id_grupo', $postulante->id_grupo)
                ->select('materia.id', 'materia.nombre')
                ->distinct()
                ->orderBy('materia.nombre')
                ->get();
        } else {
            $materias = DB::table('materia')->orderBy('nombre')->get();
        }

        // Obtener exámenes por materia
        $examenes = $materias->map(function($m) use ($codigo) {
            $exams = DB::table('examen')
                ->where('codigo_postulante', $codigo)
                ->where('id_materia', $m->id)
                ->get();

            $e1 = $exams->firstWhere('nro_examen', 1);
            $e2 = $exams->firstWhere('nro_examen', 2);
            $e3 = $exams->firstWhere('nro_examen', 3);

            $notaFinal = round(
                (($e1->nota ?? 0) * (($e1->ponderacion ?? 30) / 100)) +
                (($e2->nota ?? 0) * (($e2->ponderacion ?? 30) / 100)) +
                (($e3->nota ?? 0) * (($e3->ponderacion ?? 40) / 100)),
                1
            );

            $tieneNotas = $e1 || $e2 || $e3;

            return [
                'materia'   => $m->nombre,
                'e1'        => $e1,
                'e2'        => $e2,
                'e3'        => $e3,
                'notaFinal' => $tieneNotas ? $notaFinal : null,
                'aprobado'  => $tieneNotas ? $notaFinal >= 60 : null,
            ];
        })->filter(fn($e) => $e['e1'] || $e['e2'] || $e['e3']);

        $grupo = DB::table('grupo')
            ->where('id', $postulante->id_grupo)
            ->where('codigo_gestion', $postulante->gestion_grupo)
            ->first();

        return view('rendimiento.detalle', compact('postulante', 'examenes', 'grupo', 'esDocente'));
    }
}
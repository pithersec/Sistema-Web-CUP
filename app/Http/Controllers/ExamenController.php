<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Grupo;
use App\Models\Postulante;
use App\Models\Bitacora;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamenController extends Controller
{
    /**
     * 1. obtenerGrupoYMaterias()
     * Carga los listados iniciales y filtra los postulantes si ya se seleccionó un grupo en la interfaz.
     */
    public function obtenerGrupoYMaterias(Request $request)
    {
        // El usuario logueado está amarrado a 'personal' por su 'registro_personal'
        $registroPersonal = Auth::user()->registro_personal;

        if (empty($registroPersonal)) {
            abort(403, 'Su usuario no tiene un registro de personal asignado.');
        }

        // Gestiones
        $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
        $gestionCodigo = $request->input('gestion', $gestiones->first()?->codigo);

        // Buscamos en la tabla intermedia grupo_materia las asignaciones del docente
        $grupos = DB::table('grupo_materia')
            ->join('grupo', function($join) {
                $join->on('grupo_materia.id_grupo', '=', 'grupo.id')
                    ->on('grupo_materia.gestion_grupo', '=', 'grupo.codigo_gestion');  // ← agregar
            })
            ->where('grupo_materia.registro_personal', $registroPersonal)
            ->where('grupo.codigo_gestion', $gestionCodigo)
            ->select('grupo.id', 'grupo.turno', 'grupo.aula')
            ->groupBy('grupo.id', 'grupo.turno', 'grupo.aula')
            ->orderByRaw("CASE WHEN grupo.turno = 'mañana' THEN 0 WHEN grupo.turno = 'tarde' THEN 1 ELSE 2 END, grupo.id")
            ->get();


        $materias = DB::table('grupo_materia')
            ->join('materia', 'grupo_materia.id_materia', '=', 'materia.id')
            ->where('grupo_materia.registro_personal', $registroPersonal)
            ->select('materia.id', 'materia.nombre')
            ->distinct()
            ->get();

        // Estructura estática para emular las opciones del select de exámenes de la vista
        $examenes = [
            (object)['id' => 1, 'nombre_evaluacion' => 'Examen 1', 'porcentaje' => 30],
            (object)['id' => 2, 'nombre_evaluacion' => 'Examen 2', 'porcentaje' => 30],
            (object)['id' => 3, 'nombre_evaluacion' => 'Examen 3', 'porcentaje' => 40],
        ];

        $postulantes = null;

        // Si el docente presionó "Cargar", buscamos los postulantes del grupo inyectando su nota actual si existe
        // Cambia esto en la línea 51 de tu controlador:
        if ($request->filled('id_grupo') && $request->filled('id_materia') && $request->filled('nro_examen')) { // 👈 Cambiado id_examen por nro_examen
            $postulantes = Postulante::where('id_grupo', $request->id_grupo)
                ->where('gestion_grupo', $gestionCodigo)
                ->get() // Quité el ->with('datosPersonales') si es que tus campos de nombre están directo en la tabla postulantes
                ->map(function($postulante) use ($request) {
                    // Buscamos si este alumno ya tiene una calificación guardada en este examen y materia
                    $notaGuardada = Examen::where('codigo_postulante', $postulante->codigo)
                        ->where('id_materia', $request->id_materia)
                        ->where('nro_examen', $request->nro_examen) // 👈 Cambiado id_examen por nro_examen
                        ->first();
                    // Inyectamos la nota de forma dinámica para que el Blade la pinte en el value del input
                    $postulante->nota_actual = $notaGuardada ? $notaGuardada->nota : null;
                    return $postulante;
                });
        }

        return view('examenes.index', compact('grupos', 'materias', 'examenes', 'postulantes', 'gestiones', 'gestionCodigo')); 
    }

    /**
     * 2. registrarNotas()
     * Recibe la colección de notas de la planilla, calcula la ponderación automatizada y audita en Bitácora.
     */
    public function registrarNotas(Request $request)
    {
        // Validamos la metadata de la planilla y el arreglo de calificaciones entrantes
        $request->validate([
            'id_grupo'   => 'required|exists:grupo,id',
            'id_materia' => 'required|exists:materia,id',
            'nro_examen'  => 'required|integer|between:1,3',
            'notas'      => 'required|array',
            'notas.*'    => 'nullable|numeric|between:0,100',
        ]);

        $nroExamen = (int)$request->input('nro_examen');
        
        // REGLA DE NEGOCIO: Asignar ponderación automática por arquitectura
        $ponderacionCorrecta = ($nroExamen === 3) ? 40 : 30;

        $user = Auth::user();
        $contadorNotas = 0;

        DB::beginTransaction();

        try {
            // Recorremos el lote mapeado desde el formulario Blade [codigo_postulante => valor_nota]
            foreach ($request->input('notas') as $codigoPostulante => $nota) {
                // Si el docente dejó el campo vacío, saltamos al siguiente postulante sin machacar datos
                if (is_null($nota)) {
                    continue; 
                }

                // GuardarExámen: Inserta si es nuevo o actualiza si ya existía la combinación
                Examen::updateOrCreate(
                    [
                        'codigo_postulante' => $codigoPostulante,
                        'nro_examen'        => $nroExamen,
                        'id_materia'        => $request->id_materia
                    ],
                    [
                        'ponderacion'       => $ponderacionCorrecta,
                        'nota'              => $nota,
                        'fecha'             => now()->toDateString(),
                    ]
                );

                $contadorNotas++;
            }

            // Registrar evento de auditoría obligatorio en la Bitácora del Sistema si se alteró algún registro
            if ($contadorNotas > 0) {
                Bitacora::create([
                    'ip'         => $request->ip(),
                    'accion'     => "Registro de Notas. Docente: {$user->user_name} registró/actualizó {$contadorNotas} calificaciones del Examen Nro: {$nroExamen}.",
                    'fecha_hora' => now(),
                    'id_usuario' => $user->id
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', "¡Planilla de calificaciones guardada con éxito! Se procesaron {$contadorNotas} registros.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Ocurrió un error. Verifique los datos e intente nuevamente.'
            ]);
        }
    }

    public function obtenerPostulantes($id_grupo)
    {
        $postulantes = Postulante::where('id_grupo', $id_grupo)->get();
        return response()->json($postulantes);
    }
}

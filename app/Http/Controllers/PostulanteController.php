<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Postulante;
use App\Models\DatosPersonales;
use App\Models\RequisitosPostulante;
use App\Models\Bitacora;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostulanteController extends Controller
{
    /**
     * Muestra la vista de preinscripción inyectando las carreras reales
     */
    public function mostrarFormularioPreinscripcion()
    {
        $carreras = Carrera::all(); 
        return view('preinscripcion.index', compact('carreras'));
    }

    /**
     * CU-03: Realizar preinscripción - registrarPostulante()
     * Adaptado para responder con redirecciones Web/Blade.
     */
    public function registrarPostulante(Request $request)
    {
        // 1. Validar los datos de entrada según los campos de la vistaPreinscripción
        $validated = $request->validate([
            'ci'               => 'required|string|max:11|unique:datos_personales,ci',
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'genero'           => 'required|in:m,f',
            'fecha_nac'        => 'required|date',
            'correo'           => 'required|email|unique:datos_personales,correo|max:150',
            'direccion'        => 'required|string|max:200',
            'telefono'         => 'nullable|string|max:20',
            'codigo_carrera1'  => 'required|string',
            'codigo_carrera2'  => 'required|string|different:codigo_carrera1',
            
            // Los agregamos como opcionales por si la vista no los envía en el paso 1
            'procedencia'      => 'nullable|string|max:100',
            'telefono_2'       => 'nullable|string|max:20',
            'gestion_egreso'   => 'nullable|string|max:20',
        ]);

        // Iniciamos la transacción para asegurar atomicidad
        DB::beginTransaction();

        try {
            // 2. Simular guardarDatos() en DatosPersonales
            $datosPersonales = DatosPersonales::create([
                'ci'        => $validated['ci'],
                'nombre'    => $validated['nombre'],
                'apellido'  => $validated['apellido'],
                'genero'    => $validated['genero'],
                'telefono'  => $validated['telefono'],
                'correo'    => $validated['correo'],
                'fecha_nac' => $validated['fecha_nac'],
                'direccion' => $validated['direccion'],
            ]);

            // 3. Simular guardarRequisitos() en RequisitosPostulante
            $requisitos = RequisitosPostulante::create([
                'titulo_original'  => false,
                'titulo_copia'     => false,
                'fotocopia_carnet' => false,
                'formulario'       => false,
                'comprobante'      => false,
                'libreta'          => false,
            ]);

            // Generar un código único para el postulante
            $codigoPostulante = 'P-' . date('Y') . '-' . strtoupper(Str::random(5));

            // 4. Simular guardarPostulante() en la entidad central
            $postulante = Postulante::create([
                'codigo'                   => $codigoPostulante,
                'ci'                       => $datosPersonales->ci,
                'procedencia'              => $validated['procedencia'] ?? 'Santa Cruz',
                'telefono_2'               => $validated['telefono_2'] ?? null,
                'plazo'                    => now()->addDays(5), // 5 días de plazo
                'estado'                   => 'preinscrito',
                'gestion_egreso'           => $validated['gestion_egreso'] ?? date('Y'),
                'id_requisitos_postulante' => $requisitos->id,
                'id_colegio'               => null,
                'id_pago'                  => null,
                'id_grupo'                 => null,
            ]);

            // 5. Guardar carreras en postulante_carrera
            $carrera1 = $request->input('codigo_carrera1');
            $carrera2 = $request->input('codigo_carrera2');

            if ($carrera1) {
                [$codigo1, $plan1, $modalidad1] = explode('|', $carrera1);
                DB::table('postulante_carrera')->insert([
                    'codigo_postulante' => $postulante->codigo,
                    'codigo_carrera'    => $codigo1,
                    'plan_carrera'      => $plan1,
                    'modalidad_carrera' => $modalidad1,
                    'opcion'            => 1,
                ]);
            }

            if ($carrera2) {
                [$codigo2, $plan2, $modalidad2] = explode('|', $carrera2);
                DB::table('postulante_carrera')->insert([
                    'codigo_postulante' => $postulante->codigo,
                    'codigo_carrera'    => $codigo2,
                    'plan_carrera'      => $plan2,
                    'modalidad_carrera' => $modalidad2,
                    'opcion'            => 2,
                ]);
            }

            // Consolidamos los cambios en la Base de Datos
            DB::commit();

            // CONEXIÓN BLADE: Guardamos la información clave del éxito y redirigimos
            return redirect()->route('preinscripcion.exito')->with([
                'success'           => '¡Preinscripción realizada con éxito!',
                'codigo_postulante' => $postulante->codigo,
                'plazo_limite'      => $postulante->plazo->format('d/m/Y')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Ocurrió un error. Verifique los datos e intente nuevamente.'
            ]);
        }
    }
/**
     * CU-13: Listar Postulantes con búsquedas, paginación y filtros dinámicos.
     */
    public function listarPostulantes(Request $request)
    {
        // Gestiones para el selector
        $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();

        $gestionCodigo = $request->input('gestion',
            $gestiones->first()?->codigo
        );

        $buscar = $request->input('buscar');
        $estado = $request->input('estado', 'Todos los estados');
        $carrera = $request->input('carrera', 'Todas las carreras');
        $procedencia = $request->input('procedencia', 'Todas');

        $query = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->join('grupo', function($join) {
                $join->on('postulante.id_grupo', '=', 'grupo.id')
                    ->on('postulante.gestion_grupo', '=', 'grupo.codigo_gestion');
            })
            ->where('grupo.codigo_gestion', $gestionCodigo)           // ← filtrar por gestión
            ->select(
                'postulante.codigo',
                'postulante.ci',
                'postulante.procedencia',
                'postulante.estado',
                'postulante.telefono_2',
                'datos_personales.nombre',
                'datos_personales.apellido',
                'datos_personales.telefono'
            );

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('postulante.ci', 'LIKE', "%{$buscar}%")
                ->orWhere('datos_personales.nombre', 'LIKE', "%{$buscar}%")
                ->orWhere('datos_personales.apellido', 'LIKE', "%{$buscar}%")
                ->orWhere('datos_personales.telefono', 'LIKE', "%{$buscar}%");
            });
        }

        if (!empty($carrera) && $carrera !== 'Todas las carreras') {
            [$codigoC, $planC, $modalidadC] = explode('|', $carrera);
            $query->whereExists(function($q) use ($codigoC, $planC, $modalidadC) {
                $q->select(DB::raw(1))
                ->from('postulante_carrera')
                ->whereColumn('postulante_carrera.codigo_postulante', 'postulante.codigo')
                ->where('postulante_carrera.codigo_carrera', $codigoC)
                ->where('postulante_carrera.plan_carrera', $planC)
                ->where('postulante_carrera.modalidad_carrera', $modalidadC);
            });
        }

        if (!empty($estado) && $estado !== 'Todos los estados') {
            $query->where('postulante.estado', $estado);
        }

        if (!empty($procedencia) && $procedencia !== 'Todas') {
            $query->where('postulante.procedencia', $procedencia);
        }

        $postulantes = $query->paginate(15)->withQueryString();
        $totalPostulantes = $postulantes->total();
        $carreras = Carrera::orderByRaw("CASE WHEN modalidad = 'presencial' THEN 0 ELSE 1 END")
            ->orderBy('nombre')
            ->get();

        return view('postulantes.index', compact(
            'postulantes', 'totalPostulantes', 'carreras',
            'buscar', 'estado', 'carrera',
            'gestiones', 'gestionCodigo', 'procedencia'
        ));
    }

    /**
     * CU-13: Dar de baja a un postulante (Cambio de estado administrativo)
     */
public function darBaja($codigo)
    {
        $postulante = Postulante::where('codigo', $codigo)->firstOrFail();
        $postulante->update(['estado' => 'baja']);

        if (auth()->check()) {
            Bitacora::create([
                'ip' => request()->ip(),
                'accion' => "Baja de Postulante. Usuario: " . auth()->user()->user_name . " dio de baja al postulante: {$codigo}.",
                'fecha_hora' => now(),
                'id_usuario' => auth()->id()
            ]);
        }

        return redirect()->back()->with('success', 'El postulante ha sido dado de baja correctamente.');
    }

    // CU-13: Ver detalles completos de un postulante
    public function verPostulante($codigo)
    {
        $postulante = Postulante::with([
            'datosPersonales',
            'colegio',
            'grupo',
            'pago',
            'requisitosPostulante',
        ])->where('codigo', $codigo)->firstOrFail();

        // Carreras por query directo
        $carreras = DB::table('postulante_carrera')
            ->join('carrera', function($join) {
                $join->on('postulante_carrera.codigo_carrera', '=', 'carrera.codigo')
                    ->on('postulante_carrera.plan_carrera', '=', 'carrera.plan')
                    ->on('postulante_carrera.modalidad_carrera', '=', 'carrera.modalidad');
            })
            ->where('postulante_carrera.codigo_postulante', $codigo)
            ->select(
                'carrera.codigo',
                'carrera.nombre',
                'carrera.plan',
                'carrera.modalidad',
                'postulante_carrera.opcion'
            )
            ->orderBy('postulante_carrera.opcion')
            ->get();

        return view('postulantes.show', compact('postulante', 'carreras'));
    }

    // CU-13: Editar datos personales y de postulante (solo campos básicos)
    public function editarPostulante($codigo)
    {
        $postulante = Postulante::with(['datosPersonales'])
            ->where('codigo', $codigo)->firstOrFail();

        return view('postulantes.edit', compact('postulante'));
    }

    // CU-13: Actualizar datos personales y de postulante (solo campos básicos)
    public function actualizarPostulante(Request $request, $codigo)
    {
        $postulante = Postulante::with(['datosPersonales'])
            ->where('codigo', $codigo)->firstOrFail();

        $validated = $request->validate([
            'nombre'         => 'required|string|max:100',
            'apellido'       => 'required|string|max:100',
            'genero'         => 'required|in:m,f',
            'fecha_nac'      => 'required|date',
            'correo'         => 'required|email|max:150|unique:datos_personales,correo,' . $postulante->ci . ',ci',
            'direccion'      => 'required|string|max:200',
            'telefono'       => 'nullable|string|max:20',
            'telefono_2'     => 'nullable|string|max:20',
            'procedencia'    => 'nullable|string|max:100',
            'gestion_egreso' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $postulante->datosPersonales->update([
                'nombre'    => $validated['nombre'],
                'apellido'  => $validated['apellido'],
                'genero'    => $validated['genero'],
                'fecha_nac' => $validated['fecha_nac'],
                'correo'    => $validated['correo'],
                'direccion' => $validated['direccion'],
                'telefono'  => $validated['telefono'],
            ]);

            $postulante->update([
                'telefono_2'     => $validated['telefono_2'],
                'procedencia'    => $validated['procedencia'],
                'gestion_egreso' => $validated['gestion_egreso'],
            ]);

            DB::commit();
            return redirect()->route('postulantes.show', $codigo)
                ->with('success', 'Datos actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al actualizar. Intente nuevamente.']);
        }
    }
}

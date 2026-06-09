<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Postulante;
use App\Models\DatosPersonales;
use App\Models\RequisitosPostulante;
use App\Models\Bitacora;
use App\Models\Gestion;
use Illuminate\Support\Facades\Auth;
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
        $carreras = Carrera::orderByRaw("CASE WHEN modalidad = 'presencial' THEN 0 ELSE 1 END")->orderBy('nombre')->get();
        $colegios = DB::table('colegio')->orderBy('nombre')->get();
        return view('preinscripcion.index', compact('carreras', 'colegios'));
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
            'id_colegio'       => 'required|exists:colegio,id',
            'procedencia'      => 'nullable|string|max:100',
            'telefono_2'       => 'nullable|string|max:20',
            'gestion_egreso'   => 'nullable|string|max:20',
            'turno_preferido' => 'nullable|in:mañana,tarde,noche',
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
            $gestionActiva = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->first();
            $gestionCorta = str_replace('-', '', $gestionActiva->codigo);
            $ultimoCodigo = DB::table('postulante')
                ->where('codigo', 'LIKE', $gestionCorta . '%')
                ->max(DB::raw("CAST(SUBSTRING(codigo FROM " . (strlen($gestionCorta) + 1) . ") AS INTEGER)"));
            $codigoPostulante = $gestionCorta . str_pad(($ultimoCodigo ?? 0) + 1, 4, '0', STR_PAD_LEFT);

            // 4. Simular guardarPostulante() en la entidad central
            // Postulante — con los campos correctos
            $postulante = Postulante::create([
                'codigo'                   => $codigoPostulante,
                'ci'                       => $datosPersonales->ci,
                'procedencia'              => $validated['procedencia'] ?? 'Santa Cruz',
                'telefono_2'               => $validated['telefono_2'] ?? null,
                'plazo'                    => now()->addDays(5),
                'estado'                   => 'preinscrito',
                'estado_formulario'        => 'activo',
                'nombre_turno'             => $validated['turno_preferido'] ?? null,
                'gestion_egreso'           => $validated['gestion_egreso'] ?? date('Y'),
                'id_requisitos_postulante' => $requisitos->id,
                'id_colegio'               => $validated['id_colegio'],
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
            ->leftJoin('grupo', function($join) {
                $join->on('postulante.id_grupo', '=', 'grupo.id')
                    ->on('postulante.gestion_grupo', '=', 'grupo.codigo_gestion');
            })
            ->where(function($q) use ($gestionCodigo) { // ← filtrar por gestión
                $q->where('grupo.codigo_gestion', $gestionCodigo)
                ->orWhereNull('postulante.id_grupo');  // ← incluir sin grupo
            })           
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
            'pago',
            'requisitosPostulante',
        ])->where('codigo', $codigo)->firstOrFail();

        $grupo = DB::table('grupo')
            ->where('id', $postulante->id_grupo)
            ->where('codigo_gestion', $postulante->gestion_grupo)
            ->first();

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

        return view('postulantes.show', compact('postulante', 'carreras', 'grupo'));
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

    public function actualizarRequisitos(Request $request, $codigo)
    {
        $postulante = Postulante::where('codigo', $codigo)->firstOrFail();
        
        $req = RequisitosPostulante::find($postulante->id_requisitos_postulante);
        
        $req->update([
            'titulo_original'  => $request->has('titulo_original'),
            'titulo_copia'     => $request->has('titulo_copia'),
            'fotocopia_carnet' => $request->has('fotocopia_carnet'),
            'formulario'       => $request->has('formulario'),
            'comprobante'      => $request->has('comprobante'),
            'libreta'          => $request->has('libreta'),
        ]);

        Bitacora::create([
            'ip'         => $request->ip(),
            'accion'     => "Actualización de Requisitos. Usuario: " . Auth::user()->user_name . " actualizó requisitos del postulante: {$codigo}.",
            'fecha_hora' => now(),
            'id_usuario' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Requisitos actualizados correctamente.');
    }

    public function mostrarPago($codigo)
    {
        $postulante = Postulante::with(['datosPersonales', 'requisitosPostulante'])
            ->where('codigo', $codigo)->firstOrFail();

        // Verificar si ya pagó
        if ($postulante->pago && $postulante->pago->estado === 'completado') {
            return redirect()->route('preinscripcion.exito')
                ->with('codigo_postulante', $codigo)
                ->with('success', 'Tu pago ya fue procesado.');
        }

        // Verificar requisitos completos
        $req = $postulante->requisitosPostulante;
        $requisitosCompletos = $req &&
            $req->titulo_original && $req->titulo_copia &&
            $req->fotocopia_carnet && $req->formulario &&
            $req->libreta;

        return view('preinscripcion.pago', compact('postulante', 'requisitosCompletos'));
    }

    public function confirmarPago(Request $request, $codigo)
    {
        $postulante = Postulante::with(['requisitosPostulante'])
            ->where('codigo', $codigo)->firstOrFail();

        // Verificar requisitos
        $req = $postulante->requisitosPostulante;
        $requisitosCompletos = $req &&
            $req->titulo_original && $req->titulo_copia &&
            $req->fotocopia_carnet && $req->formulario &&
            $req->libreta;

        if (!$requisitosCompletos) {
            return redirect()->back()->withErrors(['error' => 'No puedes pagar sin completar los requisitos.']);
        }

        DB::beginTransaction();
        try {
            // Crear pago
            $pago = DB::table('pago')->insertGetId([
                'monto'          => 700.00,
                'fecha'          => now()->toDateString(),
                'concepto'       => 'Inscripción CUP FICCT',
                'estado'         => 'completado',
                'id_transaccion' => 'pi_' . Str::random(32),
                'moneda'         => 'USD',
            ]);

            // Marcar comprobante como entregado
            DB::table('requisitos_postulante')
                ->where('id', $postulante->id_requisitos_postulante)
                ->update(['comprobante' => true]);

            // Actualizar postulante
            $postulante->update([
                'id_pago' => $pago,
                'estado'  => 'inscrito',
            ]);

            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Pago Completado. Postulante: {$codigo} realizó el pago de inscripción.",
                'fecha_hora' => now(),
                'id_usuario' => 1 // sistema
            ]);

            DB::commit();
            return redirect()->route('preinscripcion.exito')
                ->with('success', '¡Pago realizado con éxito! Ya estás inscrito.')
                ->with('codigo_postulante', $codigo)
                ->with('plazo_limite', now()->addDays(5)->format('d/m/Y'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al procesar el pago.']);
        }
    }
}

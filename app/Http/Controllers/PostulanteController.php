<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Postulante;
use App\Models\DatosPersonales;
use App\Models\RequisitosPostulante;
use App\Models\Bitacora;
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
            'correo'           => 'required|email|max:150',
            'direccion'        => 'required|string|max:200',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150',
            'fecha_nac'        => 'nullable|date',
            'direccion'        => 'nullable|string|max:200',
            
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
        $buscar = $request->input('buscar');
        $estado = $request->input('estado');
        $carrera = $request->input('carrera');

        // Construir la consulta uniendo la tabla pivote de datos personales
        $query = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->select(
                'postulante.codigo',
                'postulante.ci',
                'postulante.procedencia',
                'postulante.estado',
                'datos_personales.nombre',
                'datos_personales.apellido',
                'datos_personales.telefono'
            );

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('postulante.ci', 'LIKE', "%{$buscar}%")
                    ->orWhere('datos_personales.nombre', 'LIKE', "%{$buscar}%")
                    ->orWhere('datos_personales.apellido', 'LIKE', "%{$buscar}%");
            });
        }

        if (!empty($estado) && $estado !== 'Todos los estados') {
            $query->where('estado', $estado);
        }

        if (!empty($carrera) && $carrera !== 'Todas las carreras') {
            $query->whereExists(function($q) use ($carrera) {
                $q->select(DB::raw(1))
                ->from('postulante_carrera')
                ->whereColumn('postulante_carrera.codigo_postulante', 'postulante.codigo')
                ->where('postulante_carrera.codigo_carrera', $carrera);
            });
        }

        $postulantes = $query->paginate(5)->withQueryString();
        $totalPostulantes = $postulantes->total();

        $carreras = Carrera::all();

        return view('postulantes.index', compact('postulantes', 'totalPostulantes', 'carreras', 'buscar', 'estado', 'carrera'));
    }

    /**
     * CU-13: Dar de baja a un postulante (Cambio de estado administrativo)
     */
public function darBaja($codigo)
    {
        $postulante = Postulante::where('codigo', $codigo)->firstOrFail();
        $postulante->update(['estado' => 'Baja']);

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
}

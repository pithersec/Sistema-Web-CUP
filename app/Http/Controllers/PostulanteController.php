<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Postulante;
use App\Models\DatosPersonales;
use App\Models\RequisitosPostulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostulanteController extends Controller
{
    /**
     * Muestra la vista de preinscripción inyectando las carreras reales
     */
    public function mostrarFormularioPreinscripcion()
    {
        $carreras = Carrera::all(); 
        return view('preinscripcion', compact('carreras'));
    }

    /**
     * CU-03: Realizar preinscripción - registrarPostulante()
     * Adaptado para responder con redirecciones Web/Blade.
     */
    public function registrarPostulante(Request $request)
    {
        // 1. Validar los datos de entrada según los campos de la vistaPreinscripción
        $validated = $request->validate([
            'ci'               => 'required|string|max:20|unique:datos_personales,ci',
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'genero'           => 'nullable|string|max:10',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150',
            'fecha_nac'        => 'nullable|date',
            'direccion'        => 'nullable|string|max:200',
            
            // Los agregamos como opcionales por si la vista no los envía en el paso 1
            'procedencia'      => 'nullable|string|max:100',
            'telefono_2'       => 'nullable|string|max:20',
            'gestion_egreso'   => 'nullable|string|max:20',
            
            'codigo_carrera1'  => 'required|exists:carrera,codigo',
            'codigo_carrera2'  => 'required|exists:carrera,codigo|different:codigo_carrera1',
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
                'codigo_carrera1'          => $validated['codigo_carrera1'],
                'codigo_carrera2'          => $validated['codigo_carrera2'],
                'id_colegio'               => null,
                'id_pago'                  => null,
                'id_grupo'                 => null,
            ]);

            // Consolidamos los cambios en la Base de Datos
            DB::commit();

            // CONEXIÓN BLADE: Guardamos la información clave del éxito y redirigimos
            return redirect('/preinscripcion/exito')->with([
                'success'           => '¡Preinscripción realizada con éxito!',
                'codigo_postulante' => $postulante->codigo,
                'plazo_limite'      => $postulante->plazo->format('d/m/Y')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Si falla, volvemos atrás cargando los errores
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Hubo un fallo al registrar la preinscripción: ' . $e->getMessage()
            ]);
        }
    }
/**
     * CU-13: Listar Postulantes con búsquedas, paginación y filtros dinámicos.
     */
    public function listarPostulantes(Request $request)
    {
        // Capturar los valores de los filtros desde la barra de herramientas (Toolbar)
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
                'postulante.codigo_carrera1',
                'datos_personales.nombre',
                'datos_personales.apellido',
                'datos_personales.telefono'
            );

        // Filtro 1: Buscador predictivo (CI, Nombre o Apellido)
        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('postulante.ci', 'LIKE', "%{$buscar}%")
                  ->orWhere('datos_personales.nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('datos_personales.apellido', 'LIKE', "%{$buscar}%");
            });
        }

        // Filtro 2: Selector por Estado
        if (!empty($estado) && $estado !== 'Todos los estados') {
            $query->where('postulante.estado', $estado);
        }

        // Filtro 3: Selector por Carrera
        if (!empty($carrera) && $carrera !== 'Todas las carreras') {
            $query->where('postulante.codigo_carrera1', $carrera);
        }

        // Obtener el conteo total con filtros aplicados antes de paginar
        $totalPostulantes = $query->count();

        // Paginación nativa de Laravel (5 registros por página para que calce con tu diseño)
        $postulantes = $query->paginate(5)->withQueryString();

        // Cargar el catálogo de carreras para llenar el select dinámicamente
        $carreras = Carrera::all();

        return view('admin.postulantes', compact('postulantes', 'totalPostulantes', 'carreras', 'buscar', 'estado', 'carrera'));
    }

    /**
     * CU-13: Dar de baja a un postulante (Cambio de estado administrativo)
     */
    public function darBaja($codigo)
    {
        DB::table('postulante')
            ->where('codigo', $codigo)
            ->update(['estado' => 'Baja']);

        return redirect()->back()->with('success', 'El postulante ha sido dado de baja correctamente.');
    }





}

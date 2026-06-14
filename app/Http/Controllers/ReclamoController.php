<?php

namespace App\Http\Controllers;

use App\Models\Reclamo;
use App\Models\Postulante;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReclamoController extends Controller
{
    // ==========================================================================
    // MÉTODOS DEL CASO DE USO 06: PRESENTAR RECLAMO
    // ==========================================================================

    /**
     * Muestra la interfaz pública (vistaReclamo) para registrar un reclamo.
     */
    public function formularioPublico()
    {
        return view('reclamos.create');
    }
/**
     * Operación: crearReclamo()
     * Registra un nuevo reclamo y responde en formato JSON para el control interactivo del modal.
     */
    public function crearReclamo(Request $request)
    {

        $request->merge([
            'codigo_postulante' => trim($request->input('codigo_postulante'))
        ]);

        // 1. Validamos que los datos obligatorios no estén vacíos
        $validator = \Validator::make($request->all(), [
            'codigo_postulante' => 'required|string|exists:postulante,codigo',
            'dirigido'          => 'required|string|max:200',
            'descripcion'       => 'required|string',
        ], [
            'codigo_postulante.required' => 'El campo Código es obligatorio.',
            'codigo_postulante.exists'   => 'El código de postulante no se encuentra registrado.',
            'dirigido.required'          => 'El campo Dirigido es obligatorio.',
            'descripcion.required'       => 'El campo Motivo es obligatorio.',
        ]);

        // Si los datos no están completos o válidos, enviamos los errores sin recargar
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            // 2. Creamos el registro del Reclamo
            $reclamo = Reclamo::create([
                'codigo_postulante' => $request->input('codigo_postulante'),
                'dirigido'          => $request->input('dirigido'),
                'descripcion'       => $request->input('descripcion'),
                'fecha'             => now(),
                'estado'            => 'pendiente'
            ]);

            // Respondemos éxito total
            return response()->json([
                'success' => true,
                'message' => 'Reclamo creado exitosamente'
            ]);

        } catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'errors' => [$e->getMessage()]
    ], 500);
}
    }

    // ==========================================================================
    // MÉTODOS DEL CASO DE USO 12: ATENDER RECLAMOS (Y CONSULTAS COMPLEMENTARIAS)
    // ==========================================================================

    /**
     * Operación: listarReclamos()
     * Recupera el conjunto de reclamos adaptando la vista según el rol de la ruta (Admin o Público).
     */
public function listarReclamos(Request $request)
{
    $estadoFiltro = $request->input('filtroEstado');
    $query = Reclamo::with(['postulante.datosPersonales', 'personal']);

    if (!empty($estadoFiltro) && $estadoFiltro !== 'Todos') {
        $query->where('estado', $estadoFiltro);
    }

    $reclamos = $query->orderBy('fecha', 'desc')->paginate(15);

    // SEPARACIÓN ESTRICTA POR URL
    if ($request->is('admin*')) {
        // Solo si la URL empieza con /admin, va a la bandeja de gestión
        return view('reclamos.atender', compact('reclamos', 'estadoFiltro'));
    }

    // Cualquier otro acceso público va al listado/formulario del postulante
    return view('reclamos.index', compact('reclamos', 'estadoFiltro'));
}


    /**
     * Operación: mostrarReclamo()
     * Renderiza un reclamo específico detallado (Útil para flujos de revisión profunda).
     */
    public function mostrarReclamo($id)
    {
        $reclamo = Reclamo::with(['postulante.datosPersonales', 'personal'])->findOrFail($id);
        return view('reclamos.show', compact('reclamo'));
    }

    /**
     * Operación: actualizarReclamo()
     * Permite al Administrador resolver el estado de un reclamo y registrar el evento en Bitácora.
     */
    public function actualizarReclamo(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,atendido,rechazado',
        ]);

        $user = Auth::user();
        $registroPersonal = $user->registro_personal; // Atributo mapeado de tu tabla de usuarios

        if (empty($registroPersonal)) {
            return redirect()->back()->withErrors(['error' => 'Su cuenta no posee un registro de personal vinculado para firmar esta acción.']);
        }

        DB::beginTransaction();
        try {
            $reclamo = Reclamo::findOrFail($id);
            $estadoAnterior = $reclamo->estado;
            $nuevoEstado = $request->input('estado');

            // Actualización del Modelo reclamo
            $reclamo->update([
                'estado'            => $nuevoEstado,
                'registro_personal' => $registroPersonal
            ]);

            // Operación: registrarEvento() sobre el objeto bitacora
            if ($estadoAnterior !== $nuevoEstado) {
                Bitacora::create([
                    'ip'         => $request->ip(),
                    'accion'     => "Modificación: El usuario administrativo '{$user->user_name}' cambió el estado del reclamo #{$id} de '{$estadoAnterior}' a '{$nuevoEstado}'.",
                    'fecha_hora' => now(),
                    'id_usuario' => $user->id
                ]);
            }

            DB::commit();
            return redirect()->route('reclamos.index')->with('success', "El reclamo #{$id} cambió exitosamente a '{$nuevoEstado}'.");
            

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en actualizarReclamo: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error crítico al procesar la actualización del reclamo.']);
        }
    }
}
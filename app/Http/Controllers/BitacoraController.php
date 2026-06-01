<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * 1 y 2. listarEventos() / filtrarEventos() [ADAPTADO PARA WEB]
     * Recupera el historial de auditoría permitiendo búsquedas avanzadas por múltiples criterios.
     */
    public function listarEventos(Request $request)
    {
        // 1. Iniciamos la consulta cargando la relación con el usuario
        $query = Bitacora::with('usuario');

        // Capture de parámetros desde la barra de herramientas de la vista
        $fechaBusqueda = $request->input('filtroFecha');
        $usuarioBusqueda = $request->input('filtroUsuario');
        $tipoAccion = $request->input('filtroAccion');

        // Filtro por Fecha (AÑO-MES-DÍA)
        if (!empty($fechaBusqueda)) {
            $query->whereDate('fecha_hora', $fechaBusqueda);
        }

        // Filtro por ID de Usuario específico
        if (!empty($usuarioBusqueda) && $usuarioBusqueda !== 'Todos los usuarios') {
            $query->where('id_usuario', $usuarioBusqueda);
        }

        // Filtro por Tipo de Acción usando cláusulas LIKE descriptivas
        if (!empty($tipoAccion) && $tipoAccion !== 'Todas las acciones') {
            // Adaptación inteligente para mapear selecciones con cadenas reales en tu bitácora
            if ($tipoAccion === 'Inicio de sesión') {
                $query->where('accion', 'LIKE', '%Inicio de sesión%');
            } elseif ($tipoAccion === 'Cierre de sesión') {
                $query->where('accion', 'LIKE', '%Cierre de sesión%');
            } elseif ($tipoAccion === 'Registro') {
                $query->where(function($q) {
                    $q->where('accion', 'LIKE', '%Asignación%')
                      ->orWhere('accion', 'LIKE', '%Creación%')
                      ->orWhere('accion', 'LIKE', '%Registro%');
                });
            } elseif ($tipoAccion === 'Modificación') {
                $query->where('accion', 'LIKE', '%Modificación%')
                      ->orWhere('accion', 'LIKE', '%Actualización%');
            } elseif ($tipoAccion === 'Eliminación') {
                $query->where('accion', 'LIKE', '%Eliminación%');
            }
        }

        // 2. Ordenar cronológicamente de forma descendente y paginar de 15 en 15 registros
        $eventos = $query->orderBy('fecha_hora', 'desc')->paginate(15)->withQueryString();
        
        // Contador global de registros que cumplen los filtros actuales
        $totalEventos = $query->count();

        // 3. Obtener todos los operadores del sistema para poblar el selector de filtros
        $usuarios = User::orderBy('user_name', 'asc')->get();

        return view('admin.bitacora', compact(
            'eventos', 
            'totalEventos', 
            'usuarios', 
            'fechaBusqueda', 
            'usuarioBusqueda', 
            'tipoAccion'
        ));
    }

    /**
     * 3. obtenerDetalle() [OPCIONAL - PARA MODALES DE AUDITORÍA DETALLADA]
     */
    public function obtenerDetalle($id)
    {
        $evento = Bitacora::with('usuario')->findOrFail($id);
        return response()->json($evento, 200);
    }
}
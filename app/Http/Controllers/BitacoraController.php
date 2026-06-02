<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Usuario; // CORREGIDO: Importar tu modelo personalizado
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * CU-19: listarEventos() / filtrarEventos()
     */
    public function listarEventos(Request $request)
    {
        $query = Bitacora::with('usuario');

        // Capture de parámetros desde los inputs de la vista
        $fechaBusqueda = $request->input('filtroFecha');
        $usuarioBusqueda = $request->input('filtroUsuario');
        $tipoAccion = $request->input('filtroAccion');

        // Filtro por Fecha
        if (!empty($fechaBusqueda)) {
            $query->whereDate('fecha_hora', $fechaBusqueda);
        }

        // Filtro por ID de Usuario específico
        if (!empty($usuarioBusqueda) && $usuarioBusqueda !== 'Todos los usuarios') {
            $query->where('id_usuario', $usuarioBusqueda);
        }

        // Filtro por Tipo de Acción inteligente
        if (!empty($tipoAccion) && $tipoAccion !== 'Todas las acciones') {
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

        // Paginación y conteo corregido de forma nativa
        $eventos = $query->orderBy('fecha_hora', 'desc')->paginate(15)->withQueryString();
        $totalEventos = $eventos->total(); // <--- SOLUCIÓN: Extrae el total real directamente del paginador

        // Obtener todos los operadores reales de tu tabla 'usuario'
        $usuarios = Usuario::orderBy('user_name', 'asc')->get();

        return view('admin.bitacora', compact(
            'eventos', 
            'totalEventos', 
            'usuarios', 
            'fechaBusqueda', 
            'usuarioBusqueda', 
            'tipoAccion'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BitacoraController extends Controller
{
    /**
     * CU-19: listarEventos() / filtrarEventos()
     */
    public function listarEventos(Request $request)
    {
        try {
            $query = Bitacora::with('usuario');

            $fechaBusqueda = $request->input('filtroFecha');
            $usuarioBusqueda = $request->input('filtroUsuario');
            $tipoAccion = $request->input('filtroAccion');

            if (!empty($fechaBusqueda)) {
                $query->whereDate('fecha_hora', $fechaBusqueda);
            }

            if (!empty($usuarioBusqueda) && $usuarioBusqueda !== 'Todos los usuarios') {
                $query->where('id_usuario', $usuarioBusqueda);
            }

            if (!empty($tipoAccion) && $tipoAccion !== 'Todas las acciones') {
            if ($tipoAccion === 'Inicio de sesión') {
                $query->where('accion', 'LIKE', '%Inicio de sesión%');
            } elseif ($tipoAccion === 'Cierre de sesión') {
                $query->where('accion', 'LIKE', '%Cierre de sesión%');
            } elseif ($tipoAccion === 'Registro') {
                $query->where(function($q) {
                    $q->where('accion', 'LIKE', '%Registro de Docente%')
                    ->orWhere('accion', 'LIKE', '%Registro de Postulante%')
                    ->orWhere('accion', 'LIKE', '%Registro de Notas%')
                    ->orWhere('accion', 'LIKE', '%Asignación%')
                    ->orWhere('accion', 'LIKE', '%Creación%');
                });
            } elseif ($tipoAccion === 'Modificación') {
                $query->where(function($q) {
                    $q->where('accion', 'LIKE', '%Modificación%')
                    ->orWhere('accion', 'LIKE', '%Actualización%');
                });
            } elseif ($tipoAccion === 'Activación') {
                $query->where('accion', 'LIKE', 'Activación%');
            } elseif ($tipoAccion === 'Eliminación') {
                $query->where(function($q) {
                    $q->where('accion', 'LIKE', '%Baja%')
                    ->orWhere('accion', 'LIKE', 'Desactivación%')
                    ->orWhere('accion', 'LIKE', '%Eliminación%');
                });
            }
        }

            $eventos = $query->orderBy('fecha_hora', 'desc')->paginate(15)->withQueryString();
            $totalEventos = $eventos->total();

            $usuarios = Usuario::orderBy('user_name', 'asc')->get();

            return view('admin.bitacora', compact(
                'eventos', 
                'totalEventos', 
                'usuarios', 
                'fechaBusqueda', 
                'usuarioBusqueda', 
                'tipoAccion'
            ));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error. Verifique los datos e intente nuevamente.']);
        }
    }

    public function obtenerDetalle($id)
    {
        $evento = Bitacora::with('usuario')->findOrFail($id);
        return response()->json($evento);
    }
}

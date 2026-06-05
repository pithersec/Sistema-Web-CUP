<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\BitacoraController;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS (Invitados / Sin Autenticación)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [UsuarioController::class, 'enviarCredenciales']);

Route::get('/preinscripcion', [PostulanteController::class, 'mostrarFormularioPreinscripcion'])->name('preinscripcion.form');
Route::post('/preinscripcion', [PostulanteController::class, 'registrarPostulante'])->name('preinscripcion.registrar');
Route::get('/preinscripcion/exito', function () {
    //if (!session('success')) {
    //    return redirect()->route('preinscripcion.form');
    //}
    return view('preinscripcion.exito');
})->name('preinscripcion.exito');

/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (Requieren Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/logout-confirm', function () {
        return view('auth.logout');
    });
    Route::post('/logout', [UsuarioController::class, 'cerrarSesión'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE USUARIOS (Sistema y Administrador)
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:usuarios.ver')->group(function () {
        Route::get('/admin/usuarios', [UsuarioController::class, 'listarUsuarios'])->name('usuarios.index');
    });
    Route::middleware('privilegio:usuarios.crear')->group(function () {
        Route::get('/admin/usuarios/crear', [UsuarioController::class, 'crearUsuario'])->name('usuarios.create');
        Route::post('/admin/usuarios', [UsuarioController::class, 'guardarUsuario'])->name('usuarios.store');
    });
    Route::middleware('privilegio:usuarios.editar')->group(function () {
        Route::get('/admin/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario'])->name('usuarios.edit');
        Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.update');
    });
    Route::middleware('privilegio:usuarios.eliminar')->group(function () {
        Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'eliminarUsuario'])->name('usuarios.destroy');
    });
    Route::middleware('privilegio:perfiles.gestionar')->group(function () {
        Route::get('/admin/perfiles', [UsuarioController::class, 'gestionarPerfiles'])->name('perfiles.index');
    });

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE POSTULANTES
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:postulantes.ver')->group(function () {
        Route::get('/admin/postulantes', [PostulanteController::class, 'listarPostulantes'])->name('postulantes.index');
    });
    Route::middleware('privilegio:postulantes.aprobar')->group(function () {
        Route::put('/admin/postulantes/{codigo}', [PostulanteController::class, 'actualizarPostulante'])->name('postulantes.update');
    });
    Route::middleware('privilegio:postulantes.rechazar')->group(function () {
        Route::post('/admin/postulantes/{codigo}/baja', [PostulanteController::class, 'darBaja'])->name('postulantes.baja');
    });
    Route::middleware('privilegio:postulantes.validar')->group(function () {
        Route::patch('/admin/postulantes/{codigo}/desactivar', [PostulanteController::class, 'desactivarPostulante'])->name('postulantes.desactivar');
    });

    /*
    |------------------------------------------------------------------
    | MÓDULO DOCENTE / EVALUACIÓN
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:notas.ver')->group(function () {
        Route::get('/docente/registrar-notas', [ExamenController::class, 'obtenerGrupoYMaterias'])->name('notas.index');
        Route::get('/docente/grupos-materias', [ExamenController::class, 'obtenerGrupoYMaterias']);
        Route::get('/docente/grupos/{id_grupo}/postulantes', [ExamenController::class, 'obtenerPostulantes']);
    });
    Route::middleware('privilegio:notas.registrar')->group(function () {
        Route::post('/docente/registrar-notas', [ExamenController::class, 'registrarNotas'])->name('notas.registrar');
    });

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE DOCENTES Y PERSONAL ADMINISTRATIVO
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:docentes.ver')->group(function () {
        Route::get('/admin/docentes', [PersonalController::class, 'listarDocentes'])->name('docentes.index');
    });
    Route::middleware('privilegio:docentes.crear')->group(function () {
        Route::post('/admin/docentes', [PersonalController::class, 'guardarDocente'])->name('docentes.guardar');
    });
    Route::middleware('privilegio:docentes.editar')->group(function () {
        Route::put('/admin/docentes/{registro}', [PersonalController::class, 'actualizarDocente'])->name('docentes.actualizar');
    });
    Route::middleware('privilegio:docentes.desactivar')->group(function () {
        Route::post('/admin/docentes/{registro}/baja', [PersonalController::class, 'desactivarDocente']);
        Route::patch('/admin/docentes/{registro}/desactivar', [PersonalController::class, 'desactivarDocente'])->name('docentes.desactivar');
    });

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE CARRERAS Y CONFIGURACIÓN DE CUPOS
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:carreras.ver')->group(function () {
        Route::get('/admin/carreras', [CarreraController::class, 'listarCarreras'])->name('carreras.index');
        Route::get('/admin/carreras-cupos', [CarreraController::class, 'listarCarreras']);
    });
    Route::middleware('privilegio:cupos.editar')->group(function () {
        Route::post('/admin/carreras/cupos', [CarreraController::class, 'guardarCupos']);
        Route::put('/admin/carreras/cupos/{id_carrera_gestion}', [CarreraController::class, 'actualizarCupos']);
        Route::post('/admin/carreras-cupos/guardar-masivo', [CarreraController::class, 'guardarMasivo'])->name('carreras.guardarMasivo');
        Route::post('/admin/carreras-cupos/guardar-fila', [CarreraController::class, 'guardarCuposFila'])->name('carreras.guardarFila');
    });

    /*
    |------------------------------------------------------------------
    | AUDITORÍA DE SEGURIDAD (Bitácora del Sistema)
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:bitacora.ver')->group(function () {
        Route::get('/admin/bitacora', [BitacoraController::class, 'listarEventos'])->name('bitacora.index');
        Route::get('/admin/bitacora/{id}', [BitacoraController::class, 'obtenerDetalle'])->name('bitacora.show');
    });
});
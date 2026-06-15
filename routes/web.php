<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\ReclamoController;
use App\Http\Controllers\RendimientoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS (Invitados / Sin Autenticación)
|--------------------------------------------------------------------------
*/

// Inicio y autenticación
Route::get('/', fn() => view('auth.welcome'))->name('welcome');
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [UsuarioController::class, 'enviarCredenciales']);
Route::get('/forgot-password', fn() => view('auth.forgot_password'));
Route::post('/forgot-password', fn() => back()->with('status', 'sent'));

// CU-03: Preinscripción
Route::get('/preinscripcion', [PostulanteController::class, 'mostrarFormularioPreinscripcion'])->name('preinscripcion.form');
Route::post('/preinscripcion', [PostulanteController::class, 'registrarPostulante'])->name('preinscripcion.registrar');
Route::get('/preinscripcion/exito', function () {
    if (!session('success')) return redirect()->route('preinscripcion.form');
    return view('preinscripcion.exito');
})->name('preinscripcion.exito');

// CU-04: Pago
Route::post('/pago/exitoso', [PagoController::class, 'pagoExitoso'])->name('pago.exitoso');
Route::get('/pago/{codigo}', [PagoController::class, 'mostrarPago'])->name('pago.index');
Route::post('/pago/{codigo}/iniciar', [PagoController::class, 'iniciarPago'])->name('pago.iniciar');
Route::post('/pago/webhook', [PagoController::class, 'pagoWebhook'])->name('pago.webhook');

// CU-05: Consultar estado de admisión
Route::get('/estado', [PostulanteController::class, 'mostrarFormularioEstado'])->name('estado.form');
Route::post('/estado', [PostulanteController::class, 'consultarEstado'])->name('estado.consultar');

// CU-06: Reclamos (público)
Route::get('/reclamos', [ReclamoController::class, 'listarReclamos'])->name('reclamos.publico');
Route::post('/reclamos/guardar', [ReclamoController::class, 'crearReclamo'])->name('reclamos.store');

/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (Requieren Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Sesión
    Route::get('/logout-confirm', fn() => view('auth.logout'));
    Route::post('/logout', [UsuarioController::class, 'cerrarSesión'])->name('logout');

    // CU-09: Dashboard / Indicadores
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |------------------------------------------------------------------
    | P4 · SEGURIDAD Y REPORTES
    |------------------------------------------------------------------*/

    // CU-10: Reportes
    Route::middleware('privilegio:reportes.ver')->group(function () {
        Route::get('/admin/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/admin/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
    });

    // CU-19: Bitácora
    Route::middleware('privilegio:bitacora.ver')->group(function () {
        Route::get('/admin/bitacora', [BitacoraController::class, 'listarEventos'])->name('bitacora.index');
        Route::get('/admin/bitacora/{id}', [BitacoraController::class, 'obtenerDetalle'])->name('bitacora.show');
    });

    /*
    |------------------------------------------------------------------
    | P3 · GESTIÓN ADMINISTRATIVA
    |------------------------------------------------------------------*/

    // CU-13: Gestionar postulantes
    Route::middleware('privilegio:postulantes.ver')->group(function () {
        Route::get('/admin/postulantes', [PostulanteController::class, 'listarPostulantes'])->name('postulantes.index');
        Route::get('/admin/postulantes/{codigo}', [PostulanteController::class, 'verPostulante'])->name('postulantes.show');
    });
    Route::middleware('privilegio:postulantes.editar')->group(function () {
        Route::get('/admin/postulantes/{codigo}/editar', [PostulanteController::class, 'editarPostulante'])->name('postulantes.edit');
        Route::put('/admin/postulantes/{codigo}', [PostulanteController::class, 'actualizarPostulante'])->name('postulantes.update');
        Route::patch('/admin/postulantes/{codigo}/requisitos', [PostulanteController::class, 'actualizarRequisitos'])->name('postulantes.requisitos');
        Route::patch('/admin/postulantes/{codigo}/desactivar', [PostulanteController::class, 'desactivarPostulante'])->name('postulantes.desactivar');
    });
    Route::middleware('privilegio:postulantes.baja')->group(function () {
        Route::post('/admin/postulantes/{codigo}/baja', [PostulanteController::class, 'darBaja'])->name('postulantes.baja');
    });

    // CU-14: Gestionar personal
    Route::middleware('privilegio:personal.ver')->group(function () {
        Route::get('/admin/personal', [PersonalController::class, 'listarDocentes'])->name('personal.index');
        Route::get('/admin/personal/{registro}', [PersonalController::class, 'verDocente'])->name('personal.show');
    });
    Route::middleware('privilegio:personal.crear')->group(function () {
        Route::get('/admin/personal/crear', [PersonalController::class, 'crearDocente'])->name('personal.crear');
        Route::post('/admin/personal', [PersonalController::class, 'guardarDocente'])->name('personal.guardar');
    });
    Route::middleware('privilegio:personal.editar')->group(function () {
        Route::get('/admin/personal/{registro}/editar', [PersonalController::class, 'editarDocente'])->name('personal.edit');
        Route::put('/admin/personal/{registro}', [PersonalController::class, 'actualizarDocente'])->name('personal.actualizar');
    });
    Route::middleware('privilegio:personal.desactivar')->group(function () {
        Route::patch('/admin/personal/{registro}/desactivar', [PersonalController::class, 'desactivarDocente'])->name('personal.desactivar');
        Route::patch('/admin/personal/{registro}/activar', [PersonalController::class, 'activarDocente'])->name('personal.activar');
    });

    // CU-15: Gestionar carreras y cupos
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

    // CU-16: Gestionar usuarios y perfiles
    Route::middleware('privilegio:usuarios.ver')->group(function () {
        Route::get('/admin/usuarios', [UsuarioController::class, 'listarUsuarios'])->name('usuarios.index');
    });
    Route::middleware('privilegio:usuarios.editar')->group(function () {
        Route::get('/admin/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario'])->name('usuarios.edit');
        Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.update');
    });
    Route::middleware('privilegio:usuarios.cargar')->group(function () {
        Route::get('/admin/usuarios/cargar', [UsuarioController::class, 'mostrarCargaMasiva'])->name('usuarios.cargar');
        Route::post('/admin/usuarios/cargar', [UsuarioController::class, 'procesarCargaMasiva'])->name('usuarios.procesarCarga');
        Route::post('/admin/usuarios/cargar-requisitos', [UsuarioController::class, 'procesarCargaRequisitos'])->name('usuarios.procesarRequisitos');
    });
    Route::middleware('privilegio:perfiles.gestionar')->group(function () {
        Route::get('/admin/perfiles', [UsuarioController::class, 'gestionarPerfiles'])->name('perfiles.index');
        Route::put('/admin/perfiles/{id}/privilegios', [UsuarioController::class, 'actualizarPrivilegios'])->name('perfiles.privilegios');
    });

    // CU-18: Configurar parámetros
    Route::middleware('privilegio:configuracion.gestionar')->group(function () {
        Route::get('/admin/parametros', [ParametroController::class, 'mostrarParametros'])->name('parametros.index');
        Route::put('/admin/parametros', [ParametroController::class, 'modificarParametros'])->name('parametros.modificar');
        Route::post('/admin/parametros/cerrar', [ParametroController::class, 'cerrarGestion'])->name('parametros.cerrar');
        Route::post('/admin/parametros/abrir', [ParametroController::class, 'abrirGestion'])->name('parametros.abrir');
    });

    /*
    |------------------------------------------------------------------
    | P2 · GESTIÓN ACADÉMICA
    |------------------------------------------------------------------*/

    // CU-07: Registrar notas
    Route::middleware('privilegio:notas.ver')->group(function () {
        Route::get('/docente/registrar-notas', [ExamenController::class, 'obtenerGrupoYMaterias'])->name('notas.index');
        Route::get('/docente/grupos-materias', [ExamenController::class, 'obtenerGrupoYMaterias']);
        Route::get('/docente/grupos/{id_grupo}/postulantes', [ExamenController::class, 'obtenerPostulantes']);
    });
    Route::middleware('privilegio:notas.registrar')->group(function () {
        Route::post('/docente/registrar-notas', [ExamenController::class, 'registrarNotas'])->name('notas.registrar');
    });

    // CU-08: Rendimiento académico
    Route::middleware('privilegio:rendimiento.ver')->group(function () {
        Route::get('/rendimiento', [RendimientoController::class, 'index'])->name('rendimiento.index');
        Route::get('/rendimiento/{codigo}', [RendimientoController::class, 'detalle'])->name('rendimiento.detalle');
    });

    // CU-11: Asignación de grupos
    Route::middleware('privilegio:grupos.ver')->group(function () {
        Route::get('/admin/grupos', [GrupoController::class, 'mostrarAsignacion'])->name('grupos.index');
    });
    Route::middleware('privilegio:grupos.asignar')->group(function () {
        Route::post('/admin/grupos/generar', [GrupoController::class, 'generarGrupos'])->name('grupos.generar');
        Route::get('/admin/grupos/asignar-docente', [GrupoController::class, 'mostrarFormAsignarDocente'])->name('grupos.formDocente');
        Route::post('/admin/grupos/asignar-docente', [GrupoController::class, 'asignarDocente'])->name('grupos.asignarDocente');
    });

    // CU-12: Atender reclamos (admin)
    Route::middleware('privilegio:reclamos.gestionar')->group(function () {
        Route::get('/admin/reclamos', [ReclamoController::class, 'listarReclamos'])->name('admin.reclamos.index');
        Route::get('/admin/reclamos/{id}', [ReclamoController::class, 'mostrarReclamo'])->name('admin.reclamos.show');
        Route::put('/admin/reclamos/{id}/actualizar', [ReclamoController::class, 'actualizarReclamo'])->name('admin.reclamos.update');
    });

    // CU-20: Asistencia
    Route::middleware('privilegio:asistencia.registrar')->group(function () {
        Route::get('/asistencia', [AsistenciaController::class, 'mostrarAsistencia'])->name('asistencia.index');
        Route::post('/asistencia', [AsistenciaController::class, 'registrarAsistencia'])->name('asistencia.registrar');
    });
});
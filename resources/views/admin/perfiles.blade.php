@extends('layouts.app')

@section('title', 'Gestión de Perfiles y Privilegios - CUP')
@section('page_title', 'Gestión de Perfiles y Privilegios')

@section('content')
<style>
    .perfiles-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }
    .perfil-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 24px; margin-bottom: 20px; }
    .perfil-card h3 { color: #0d3b6e; font-size: 18px; margin-bottom: 6px; }
    .perfil-card p { color: #5a5a5a; font-size: 13px; margin-bottom: 16px; }
    .priv-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .priv-label { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; user-select: none; }
    .priv-label input { display: none; }
    .priv-label.active { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .priv-label.inactive { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }
    .tabs { display: inline-flex; gap: 0; margin-bottom: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .tab { padding: 14px 24px; font-size: 13px; font-weight: 600; cursor: pointer; color: #5a5a5a; text-decoration: none; border-bottom: 3px solid transparent; }
    .tab.active { color: #0d3b6e; border-bottom-color: #0d3b6e; background: #f8fafc; }
    .divider { border-top: 1px solid #e2e8f0; padding-top: 14px; display: flex; justify-content: flex-end; }
    .save-btn { padding: 8px 20px; background: #0d3b6e; color: white; border: none; border-radius: 6px; font-family: 'Source Sans 3', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; }
    .save-btn:hover { background: #1a5fa8; }
</style>

@php
$nombresLegibles = [
    'sistema.total'          => 'Acceso Total',
    'usuarios.ver'           => 'Ver Usuarios',
    'usuarios.editar'        => 'Editar Usuarios',
    'usuarios.cargar'        => 'Cargar Cuentas Masivas',
    'perfiles.gestionar'     => 'Gestionar Perfiles',
    'postulantes.ver'        => 'Ver Postulantes',
    'postulantes.editar'     => 'Editar Postulantes',
    'postulantes.validar'    => 'Validar Postulantes',
    'personal.ver'           => 'Ver Personal',
    'personal.crear'         => 'Registrar Personal',
    'personal.editar'        => 'Editar Personal',
    'personal.desactivar'    => 'Desactivar Personal',
    'carreras.ver'           => 'Ver Carreras',
    'cupos.editar'           => 'Editar Cupos',
    'grupos.ver'             => 'Ver Grupos',
    'grupos.asignar'         => 'Asignar Grupos',
    'materias.gestionar'     => 'Gestionar Materias',
    'gestiones.ver'          => 'Ver Gestiones',
    'gestiones.gestionar'    => 'Gestionar Gestiones',
    'notas.ver'              => 'Ver Exámenes',
    'notas.registrar'        => 'Registrar Notas',
    'notas.editar'           => 'Editar Notas',
    'bitacora.ver'           => 'Ver Bitácora',
    'reportes.ver'           => 'Ver Reportes',
    'rendimiento.ver'        => 'Ver Rendimiento Académico',
    'reclamos.gestionar'     => 'Gestionar Reclamos',
    'asistencia.registrar'   => 'Registrar Asistencia',
    'configuracion.gestionar'=> 'Configurar Parámetros',
];
@endphp

@if(session('success'))
<div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:8px;margin-bottom:20px;font-size:14px;">
    {{ session('success') }}
</div>
@endif

<div class="tabs">
    <a href="{{ route('usuarios.index') }}" class="tab">👤 Usuarios</a>
    <a href="{{ route('perfiles.index') }}" class="tab active">🔑 Perfiles y Privilegios</a>
</div>

<div class="perfiles-wrapper">
    @foreach($perfiles as $perfil)
    <div class="perfil-card">
        <h3>{{ $perfil->nombre }}</h3>
        <p>{{ $perfil->descripcion }}</p>

        <form action="{{ route('perfiles.privilegios', $perfil->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="priv-grid">
                @foreach($privilegios as $priv)
                @php $activo = $perfil->privilegios->contains('id', $priv->id); @endphp
                <label class="priv-label {{ $activo ? 'active' : 'inactive' }}" onclick="togglePriv(this)">
                    <input type="checkbox" name="privilegios[]" value="{{ $priv->id }}" {{ $activo ? 'checked' : '' }}>
                    <span>{{ $activo ? '✓' : '—' }}</span>
                    {{ $nombresLegibles[$priv->nombre] ?? $priv->nombre }}
                </label>
                @endforeach
            </div>

            <div class="divider">
                <button type="submit" class="save-btn">💾 Guardar cambios</button>
            </div>
        </form>
    </div>
    @endforeach
</div>

<script>
function togglePriv(label) {
    const cb = label.querySelector('input');
    const span = label.querySelector('span');
    setTimeout(() => {
        if (cb.checked) {
            label.className = 'priv-label active';
            span.textContent = '✓';
        } else {
            label.className = 'priv-label inactive';
            span.textContent = '—';
        }
    }, 0);
}
</script>

@endsection
@extends('layouts.app')

@section('title', 'Gestión de Perfiles y Privilegios - CUP')
@section('page_title', 'Gestión de Perfiles y Privilegios')

@section('content')
<style>
    .perfiles-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }
    .perfil-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 24px; margin-bottom: 20px; }
    .perfil-card h3 { color: #0d3b6e; font-size: 18px; margin-bottom: 6px; }
    .perfil-card p { color: #5a5a5a; font-size: 13px; margin-bottom: 16px; }
    .priv-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    .priv-badge { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    .priv-badge.active { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .priv-badge.inactive { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }
    .tabs { display: flex; gap: 0; margin-bottom: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .tab { padding: 14px 24px; font-size: 13px; font-weight: 600; cursor: pointer; color: #5a5a5a; text-decoration: none; border-bottom: 3px solid transparent; }
    .tab.active { color: #0d3b6e; border-bottom-color: #0d3b6e; background: #f8fafc; }
</style>

<div class="tabs">
        <a href="{{ route('usuarios.index') }}" class="tab">👤 Usuarios</a>
        <a href="{{ route('perfiles.index') }}" class="tab active">🔑 Perfiles y Privilegios</a>
    </div>
<div class="perfiles-wrapper">
    @if(session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:8px;margin-bottom:20px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @php
    $nombresLegibles = [
        'sistema.total'        => 'Acceso Total',
        'usuarios.ver'         => 'Ver Usuarios',
        'usuarios.crear'       => 'Crear Usuarios',
        'usuarios.editar'      => 'Editar Usuarios',
        'usuarios.eliminar'    => 'Eliminar Usuarios',
        'perfiles.gestionar'   => 'Gestionar Perfiles',
        'privilegios.gestionar'=> 'Gestionar Privilegios',
        'postulantes.ver'      => 'Ver Postulantes',
        'postulantes.aprobar'  => 'Aprobar Postulantes',
        'postulantes.rechazar' => 'Rechazar Postulantes',
        'postulantes.validar'  => 'Validar Postulantes',
        'docentes.ver'         => 'Ver Personal',
        'docentes.crear'       => 'Registrar Personal',
        'docentes.editar'      => 'Editar Personal',
        'docentes.desactivar'  => 'Desactivar Personal',
        'carreras.ver'         => 'Ver Carreras',
        'cupos.editar'         => 'Editar Cupos',
        'grupos.ver'           => 'Ver Grupos',
        'grupos.crear'         => 'Crear Grupos',
        'materias.ver'         => 'Ver Materias',
        'materias.gestionar'   => 'Gestionar Materias',
        'gestiones.ver'        => 'Ver Gestiones',
        'gestiones.gestionar'  => 'Gestionar Gestiones',
        'notas.ver'            => 'Ver Exámenes',
        'notas.registrar'      => 'Registrar Notas',
        'notas.editar'         => 'Editar Notas',
        'bitacora.ver'         => 'Ver Bitácora',
        'reportes.ver'         => 'Ver Reportes',
    ];
    @endphp

    @foreach($perfiles as $perfil)
    <div class="perfil-card">
        <h3>{{ $perfil->nombre }}</h3>
        <p>{{ $perfil->descripcion }}</p>
        <div class="priv-grid">
            @foreach($privilegios as $priv)
                @php
                    $activo = $perfil->privilegios->contains('id', $priv->id);
                @endphp
                <span class="priv-badge {{ $activo ? 'active' : 'inactive' }}">
                    {{ $activo ? '✓' : '—' }} {{ $nombresLegibles[$priv->nombre] ?? $priv->nombre }}
                </span>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
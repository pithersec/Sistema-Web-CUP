@extends('layouts.app')

@section('title', 'Panel Docente - CUP')
@section('page_title', 'Panel Docente')

@section('content')
<style>
    .dashboard-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }
    .dash-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .dash-header h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px; font-weight: 700; }
    .gestion-select-wrap { display: flex; align-items: center; gap: 10px; }
    .gestion-select-wrap label { font-size: 13px; font-weight: 600; color: #5a5a5a; white-space: nowrap; }
    .gestion-select { padding: 8px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Source Sans 3', sans-serif; font-size: 13px; color: #0d3b6e; font-weight: 600; background: white; cursor: pointer; outline: none; }
    .gestion-select:focus { border-color: #1a5fa8; }

    .bienvenida { background: white; border-radius: 10px; padding: 20px 24px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 4px solid #0d3b6e; }
    .bienvenida h3 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 16px; margin-bottom: 4px; }
    .bienvenida p { color: #5a5a5a; font-size: 13px; }

    .grupos-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .grupo-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .grupo-card-header { background: #0d3b6e; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; }
    .grupo-card-header h3 { color: white; font-family: 'Merriweather', serif; font-size: 14px; }
    .grupo-card-header span { color: rgba(255,255,255,0.7); font-size: 12px; }
    .grupo-card-body { padding: 16px 20px; }
    .grupo-meta { display: flex; gap: 16px; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #e2e8f0; }
    .grupo-meta-item { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #5a5a5a; }
    .grupo-meta-item strong { color: #0d3b6e; }

    .materias-list { display: flex; flex-direction: column; gap: 10px; }
    .materia-row { background: #f8fafc; border-radius: 8px; padding: 12px 14px; border: 1.5px solid #e2e8f0; }
    .materia-row-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .materia-nombre { font-size: 13px; font-weight: 600; color: #0d3b6e; }
    .materia-stats { display: flex; gap: 12px; font-size: 12px; color: #5a5a5a; }
    .materia-stats span { display: flex; align-items: center; gap: 4px; }
    .tasa-bar-bg { width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; }
    .tasa-bar-fill { height: 6px; border-radius: 3px; }
    .bar-green { background: #27ae60; }
    .bar-yellow { background: #f39c12; }
    .bar-red { background: #c0392b; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-green  { background: #d4f5e2; color: #1a7a3c; }
    .badge-yellow { background: #fef9e7; color: #d68910; }
    .badge-red    { background: #fde8e8; color: #c0392b; }
    .badge-blue   { background: #dceeff; color: #1a5fa8; }

    .sin-grupos { background: white; border-radius: 10px; padding: 48px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); color: #aaa; }
    .sin-grupos p { font-size: 14px; margin-top: 12px; }

    .btn-notas { display: inline-block; padding: 7px 14px; background: #dceeff; color: #1a5fa8; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; margin-top: 12px; }
    .btn-notas:hover { background: #cce3ff; }

    @media (max-width: 768px) {
        .grupos-grid { grid-template-columns: 1fr; }
        .dash-header { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="dashboard-wrapper">

    <div class="dash-header">
        <h2>Panel Docente</h2>
        <form method="GET" action="{{ route('dashboard') }}" class="gestion-select-wrap">
            <label>Gestión:</label>
            <select name="gestion" class="gestion-select" onchange="this.form.submit()">
                @foreach($gestiones as $g)
                    <option value="{{ $g->codigo }}" {{ $g->codigo == $gestionCodigo ? 'selected' : '' }}>
                        {{ $g->codigo }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bienvenida">
        <h3>Bienvenido, {{ $nombreDocente }}</h3>
        <p>Gestión {{ $gestionCodigo }} · Tus grupos y materias asignadas</p>
    </div>

    @if($grupos->isEmpty())
    <div class="sin-grupos">
        <div style="font-size:36px;">📭</div>
        <p>No tienes grupos asignados en esta gestión.</p>
    </div>
    @else
    <div class="grupos-grid">
        @foreach($grupos as $grupo)
        <div class="grupo-card">
            <div class="grupo-card-header">
                <h3>Grupo {{ $grupo['id'] }}</h3>
                <span>Turno {{ ucfirst($grupo['nombre_turno']) }} · Aula {{ $grupo['aula'] }}</span>
            </div>
            <div class="grupo-card-body">
                <div class="grupo-meta">
                    <div class="grupo-meta-item">
                        👥 <strong>{{ $grupo['total_postulantes'] }}</strong> estudiantes
                    </div>
                    <div class="grupo-meta-item">
                        📚 <strong>{{ count($grupo['materias']) }}</strong> materias
                    </div>
                </div>

                <div class="materias-list">
                    @foreach($grupo['materias'] as $materia)
                    @php
                        $tasaClase = $materia['tasa'] >= 60 ? 'bar-green' : ($materia['tasa'] >= 40 ? 'bar-yellow' : 'bar-red');
                        $badgeClase = $materia['tasa'] >= 60 ? 'badge-green' : ($materia['tasa'] >= 40 ? 'badge-yellow' : 'badge-red');
                    @endphp
                    <div class="materia-row">
                        <div class="materia-row-top">
                            <span class="materia-nombre">{{ $materia['nombre'] }}</span>
                            <span class="badge {{ $badgeClase }}">{{ $materia['tasa'] }}%</span>
                        </div>
                        <div class="materia-stats">
                            <span>✅ {{ $materia['aprobados'] }} aprobados</span>
                            <span>📝 {{ $materia['registradas'] }} notas</span>
                            @if($materia['pendientes'] > 0)
                            <span style="color:#c0392b;">⏳ {{ $materia['pendientes'] }} pendientes</span>
                            @endif
                        </div>
                        <div class="tasa-bar-bg" style="margin-top:8px;">
                            <div class="tasa-bar-fill {{ $tasaClase }}" style="width:{{ $materia['tasa'] }}%;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('notas.index') }}?id_grupo={{ $grupo['id'] }}&gestion={{ $gestionCodigo }}" class="btn-notas">
                    📝 Ir a Planilla de Notas
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
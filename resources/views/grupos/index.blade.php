@extends('layouts.app')

@section('title', 'Asignación de Grupos - CUP')
@section('page_title', 'Grupos y Asignación')

@section('content')
<style>
    .asignacion-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }

    .toolbar-form { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; }

    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        background: white;
        color: #333;
        min-width: 180px;
        cursor: pointer;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }

    .info-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 20px 24px;
    }

    .info-card h3 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 14px;
        margin: 0 0 14px 0;
    }

    .turno-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }

    .turno-row:last-child { border-bottom: none; }
    .turno-nombre { color: #5a5a5a; text-transform: capitalize; }
    .turno-count  { font-weight: 700; color: #0d3b6e; }

    .stat-total {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        color: #0d3b6e;
    }

    .grupos-calc {
        background: #f0f7ff;
        border-left: 4px solid #1a5fa8;
        border-radius: 0 8px 8px 0;
        padding: 14px 18px;
        font-size: 13px;
        color: #1a3a5c;
        line-height: 1.9;
    }

    .grupos-calc strong { color: #0d3b6e; }

    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .table-header {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-header h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 14px;
        margin: 0;
    }

    .custom-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .custom-table thead { background: #0d3b6e; color: white; }
    .custom-table th { padding: 9px 10px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: 0.4px; white-space: nowrap; }
    .custom-table td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; color: #333; vertical-align: middle; }
    .custom-table tr:last-child td { border-bottom: none; }
    .custom-table tr:hover td { background: #f8fafc; }

    .badge-turno { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; white-space: nowrap; }
    .turno-manana { background: #fff3cd; color: #856404; }
    .turno-tarde  { background: #dceeff; color: #1a5fa8; }
    .turno-noche  { background: #e9d8fd; color: #553c9a; }

    .materias-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3px 12px;
    }

    .materia-cell {
        display: flex;
        flex-direction: column;
        padding: 3px 0;
        border-bottom: 1px dashed #f1f5f9;
        font-size: 11px;
        gap: 1px;
    }

    .materia-cell:nth-last-child(-n+2) { border-bottom: none; }

    .materia-top {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .materia-nombre  { color: #5a5a5a; font-weight: 600; white-space: nowrap; }
    .materia-horario { color: #b0bec5; font-size: 10px; white-space: nowrap; }
    .docente-nombre  { color: #1a7a3c; font-weight: 600; font-size: 11px; }
    .sin-docente     { color: #b0bec5; font-size: 11px; }

    .btn-asignar {
        padding: 1px 7px;
        background: #dceeff;
        color: #1a5fa8;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-asignar:hover { background: #cce3ff; }

    .btn-reasignar {
        padding: 1px 7px;
        background: #fff3cd;
        color: #856404;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-reasignar:hover { background: #fde68a; }

    .alert-success { background: #d4f5e2; color: #1a7a3c; padding: 12px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
    .alert-error   { background: #fde8e8; color: #c0392b; padding: 12px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }

    .btn-generar {
        padding: 10px 24px;
        background: #0d3b6e;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Source Sans 3', sans-serif;
        margin-top: 16px;
    }

    .btn-generar:hover { background: #0a2d56; }

    .total-badge { background: #dceeff; color: #1a5fa8; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }

    @media (max-width: 768px) {
        .table-card { overflow-x: auto; }
        .custom-table { min-width: 560px; }
        .materias-grid { grid-template-columns: 1fr; }
    }
</style>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error">{{ session('error') }}</div>
@endif

<div class="asignacion-wrapper">

    <form action="{{ route('grupos.index') }}" method="GET" class="toolbar-form">
        <select name="gestion" class="filter-select" onchange="this.form.submit()">
            @foreach($gestiones as $g)
            <option value="{{ $g->codigo }}" {{ $g->codigo === $codigoGestion ? 'selected' : '' }}>
                Gestión {{ $g->codigo }}
            </option>
            @endforeach
        </select>
    </form>

    @if($gruposGenerados)
    <div class="table-card">
        <div class="table-header">
            <h2>Grupos — Gestión {{ $codigoGestion }}</h2>
            <span class="total-badge">{{ $grupos->count() }} grupos</span>
        </div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Grupo</th>
                    <th>Turno</th>
                    <th>Inscritos</th>
                    <th>Aula</th>
                    <th>Materias y docentes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupos as $grupo)
                @php $gms = collect($grupoMaterias[$grupo->id] ?? [])->sortBy('orden')->values(); @endphp
                <tr>
                    <td><strong>{{ $grupo->id }}</strong></td>
                    <td>
                        @php $tc = match($grupo->nombre_turno) { 'mañana' => 'turno-manana', 'tarde' => 'turno-tarde', 'noche' => 'turno-noche', default => '' }; @endphp
                        <span class="badge-turno {{ $tc }}">{{ ucfirst($grupo->nombre_turno ?? 'S/T') }}</span>
                    </td>
                    <td>{{ $grupo->total_ins }}</td>
                    <td>{{ $grupo->aula ?? '—' }}</td>
                    <td>
                        <div class="materias-grid">
                            @foreach($gms as $gm)
                            @php $doc = $gm->registro_personal ? ($docentesMap[(string)$gm->registro_personal] ?? null) : null; @endphp
                            <div class="materia-cell">
                                <div class="materia-top">
                                    <span class="materia-nombre">{{ $gm->materia_nombre }}</span>
                                    <span class="materia-horario">{{ substr($gm->hora_inicio, 0, 5) }}–{{ substr($gm->hora_fin, 0, 5) }}</span>
                                </div>
                                <div style="display:flex; align-items:center; gap:5px;">
                                    @if($doc)
                                        <span class="docente-nombre">{{ $doc->nombre }} {{ $doc->apellido }}</span>
                                        @if(Auth::user()->tienePrivilegio('grupos.asignar'))
                                        <a href="{{ route('grupos.formDocente', ['grupo' => $grupo->id, 'gestion' => $codigoGestion, 'materia' => $gm->id_materia]) }}"
                                            class="btn-reasignar">Reasignar</a>
                                        @endif
                                    @else
                                        <span class="sin-docente">Sin docente</span>
                                        @if(Auth::user()->tienePrivilegio('grupos.asignar'))
                                        <a href="{{ route('grupos.formDocente', ['grupo' => $grupo->id, 'gestion' => $codigoGestion, 'materia' => $gm->id_materia]) }}"
                                            class="btn-asignar">Asignar</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($gestion)
    <div class="info-grid">
        <div class="info-card">
            <h3>Postulantes inscritos sin grupo</h3>
            @foreach($inscritosPorTurno as $turno => $count)
            <div class="turno-row">
                <span class="turno-nombre">{{ ucfirst($turno) }}</span>
                <span class="turno-count">{{ $count }}</span>
            </div>
            @endforeach
            <div class="stat-total">
                <span>Total</span>
                <span>{{ $totalInscritos }}</span>
            </div>
        </div>

        <div class="info-card">
            <h3>Cálculo de grupos</h3>
            @if($totalInscritos > 0)
            @php $numGrupos = (int) ceil($totalInscritos / 70); @endphp
            <div class="grupos-calc">
                CEIL({{ $totalInscritos }} / 70) = <strong>{{ $numGrupos }} grupos</strong><br>
                — Mañana: <strong>{{ $distribucion['mañana'] }}</strong> grupos<br>
                — Tarde: <strong>{{ $distribucion['tarde'] }}</strong> grupos<br>
                — Noche: <strong>{{ $distribucion['noche'] }}</strong> grupos<br>
                Capacidad total: <strong>{{ $numGrupos * 70 }}</strong> postulantes
            </div>
            @else
            <div class="grupos-calc" style="border-color:#f0a500; background:#fffbea; color:#7a5800;">
                ⚠️ No hay postulantes inscritos sin grupo para esta gestión.
            </div>
            @endif

            @if(!$gruposGenerados && $totalInscritos > 0 && Auth::user()->tienePrivilegio('grupos.asignar'))
            <form action="{{ route('grupos.generar') }}" method="POST"
                    onsubmit="return confirm('¿Generar {{ $numGrupos }} grupos para la gestión {{ $codigoGestion }}?')">
                @csrf
                <input type="hidden" name="gestion" value="{{ $codigoGestion }}">
                <button type="submit" class="btn-generar">Generar grupos</button>
            </form>
            @elseif($gruposGenerados)
            <div style="margin-top:16px; font-size:13px; color:#1a7a3c; font-weight:600;">
                ✔ Grupos ya generados para esta gestión.
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
@extends('layouts.app')

@section('title', 'Panel de Indicadores - CUP')
@section('page_title', 'Panel de Indicadores')

@section('content')
<style>
    .dashboard-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    .dash-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .dash-header h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 15px;
        font-weight: 700;
    }

    /* Selector de gestión */
    .gestion-select-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .gestion-select-wrap label {
        font-size: 13px;
        font-weight: 600;
        color: #5a5a5a;
        white-space: nowrap;
    }

    .gestion-select {
        padding: 8px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 13px;
        color: #0d3b6e;
        font-weight: 600;
        background: white;
        cursor: pointer;
        outline: none;
    }

    .gestion-select:focus { border-color: #1a5fa8; }

    /* KPIs */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .kpi-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border-top: 4px solid #1a5fa8;
        transition: transform 0.2s;
    }

    .kpi-card:hover { transform: translateY(-2px); }
    .kpi-card:nth-child(2) { border-top-color: #27ae60; }
    .kpi-card:nth-child(3) { border-top-color: #c0392b; }
    .kpi-card:nth-child(4) { border-top-color: #f39c12; }

    .kpi-icon { font-size: 24px; margin-bottom: 8px; }

    .kpi-value {
        font-family: 'Merriweather', serif;
        font-size: 32px;
        font-weight: 700;
        color: #0d3b6e;
    }

    .kpi-card:nth-child(2) .kpi-value { color: #27ae60; }
    .kpi-card:nth-child(3) .kpi-value { color: #c0392b; }
    .kpi-card:nth-child(4) .kpi-value { color: #f39c12; }

    .kpi-label {
        font-size: 11px;
        color: #5a5a5a;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 600;
        margin-top: 4px;
    }

    /* Tabla */
    .section-title {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 15px;
        margin-bottom: 14px;
        font-weight: 700;
    }

    .analytics-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 680px;
    }

    .custom-table thead { background: #0d3b6e; color: white; }

    .custom-table th {
        padding: 13px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .custom-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
    }

    .custom-table tr:last-child td { border-bottom: none; }
    .custom-table tr:hover td { background: #f8fafc; }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .badge-green  { background: #d4f5e2; color: #1a7a3c; }
    .badge-red    { background: #fde8e8; color: #c0392b; }
    .badge-blue   { background: #dceeff; color: #1a5fa8; }
    .badge-purple { background: #ede9fe; color: #6d28d9; }
    .badge-gray   { background: #f1f5f9; color: #5a5a5a; }

    .tasa-wrap { display: flex; align-items: center; gap: 8px; }
    .tasa-bar-bg { flex: 1; height: 6px; background: #e2e8f0; border-radius: 3px; min-width: 50px; }
    .tasa-bar-fill { height: 6px; border-radius: 3px; background: #27ae60; }
    .tasa-text { font-size: 12px; color: #5a5a5a; white-space: nowrap; }

    /* Responsive */
    @media (max-width: 768px) {
        .cards-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .kpi-card { padding: 16px; }
        .kpi-value { font-size: 26px; }
        .kpi-icon { font-size: 20px; }
        .dash-header { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="dashboard-wrapper">

    <div class="dash-header">
        <h2>Resumen General</h2>
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

    <div class="cards-grid">
        <div class="kpi-card">
            <div class="kpi-icon">👥</div>
            <div class="kpi-value">{{ $kpis['total_inscritos'] }}</div>
            <div class="kpi-label">Total Inscritos</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">✅</div>
            <div class="kpi-value">{{ $kpis['total_aprobados'] }}</div>
            <div class="kpi-label">Total Aprobados</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">❌</div>
            <div class="kpi-value">{{ $kpis['total_reprobados'] }}</div>
            <div class="kpi-label">Total Reprobados</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">🏫</div>
            <div class="kpi-value">{{ $kpis['grupos_habilitados'] }}</div>
            <div class="kpi-label">Grupos Habilitados</div>
        </div>
    </div>

    <h2 class="section-title">Resumen por Carrera — Gestión {{ $gestionCodigo }}</h2>
    <div class="analytics-card">
        <div class="table-scroll">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Carrera</th>
                        <th>Modalidad</th>
                        <th>Cupos</th>
                        <th>Inscritos</th>
                        <th>Aprobados</th>
                        <th>Reprobados</th>
                        <th>Tasa Aprobación</th>
                        <th>Disponibilidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resumenCarreras as $carrera)
                    @php
                        $tasa = $carrera->total_postulantes > 0
                            ? round(($carrera->total_aprobados / $carrera->total_postulantes) * 100)
                            : 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $carrera->carrera_nombre }}</strong></td>
                        <td>
                            @if($carrera->modalidad === 'virtual')
                                <span class="badge badge-purple">Virtual</span>
                            @else
                                <span class="badge badge-gray">Presencial</span>
                            @endif
                        </td>
                        <td>{{ $carrera->total_cupos }}</td>
                        <td>{{ $carrera->total_postulantes }}</td>
                        <td style="color:#27ae60; font-weight:600">{{ $carrera->total_aprobados }}</td>
                        <td style="color:#c0392b; font-weight:600">{{ $carrera->total_reprobados }}</td>
                        <td>
                            <div class="tasa-wrap">
                                <div class="tasa-bar-bg">
                                    <div class="tasa-bar-fill" style="width:{{ $tasa }}%"></div>
                                </div>
                                <span class="tasa-text">{{ $tasa }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($carrera->total_aprobados >= $carrera->total_cupos && $carrera->total_cupos > 0)
                                <span class="badge badge-red">Lleno</span>
                            @elseif($carrera->total_postulantes > 0)
                                <span class="badge badge-green">Disponible</span>
                            @else
                                <span class="badge badge-blue">En curso</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:#5a5a5a; padding:32px;">
                            No hay datos para esta gestión.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
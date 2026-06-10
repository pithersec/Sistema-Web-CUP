@extends('layouts.app')

@section('title', 'Panel de Indicadores - CUP')
@section('page_title', 'Panel de Indicadores')

@section('content')
<style>
    .dashboard-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }
    .dash-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .dash-header h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px; font-weight: 700; }
    .gestion-select-wrap { display: flex; align-items: center; gap: 10px; }
    .gestion-select-wrap label { font-size: 13px; font-weight: 600; color: #5a5a5a; white-space: nowrap; }
    .gestion-select { padding: 8px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Source Sans 3', sans-serif; font-size: 13px; color: #0d3b6e; font-weight: 600; background: white; cursor: pointer; outline: none; }
    .gestion-select:focus { border-color: #1a5fa8; }
    .cards-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-bottom: 28px; }
    .kpi-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-top: 4px solid #1a5fa8; transition: transform 0.2s; }
    .kpi-card:hover { transform: translateY(-2px); }
    .kpi-card:nth-child(1) { border-top-color: #1a5fa8; }
    .kpi-card:nth-child(1) .kpi-value { color: #0d3b6e; }
    .kpi-card:nth-child(2) { border-top-color: #2c038b; }
    .kpi-card:nth-child(2) .kpi-value { color: #2c038b; }
    .kpi-card:nth-child(3) { border-top-color: #2793ae; }
    .kpi-card:nth-child(3) .kpi-value { color: #27ae60; }
    .kpi-card:nth-child(4) { border-top-color: #c0392b; }
    .kpi-card:nth-child(4) .kpi-value { color: #c0392b; }
    .kpi-card:nth-child(5) { border-top-color: #0891b2; }
    .kpi-card:nth-child(5) .kpi-value { color: #0891b2; }
    .kpi-card:nth-child(6) { border-top-color: #f39c12; }
    .kpi-card:nth-child(6) .kpi-value { color: #f39c12; }
    .kpi-icon { font-size: 24px; margin-bottom: 8px; }
    .kpi-value { font-family: 'Merriweather', serif; font-size: 32px; font-weight: 700; color: #0d3b6e; }
    .kpi-label { font-size: 11px; color: #5a5a5a; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 600; margin-top: 4px; }
    .section-title { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px; margin-bottom: 14px; font-weight: 700; }
    @media (max-width: 768px) {
        .cards-grid { grid-template-columns: repeat(3, 1fr); gap: 12px; }
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
            <div class="kpi-icon">🎯</div>
            <div class="kpi-value">{{ $kpis['cupos_totales'] }}</div>
            <div class="kpi-label">Cupos Totales</div>
        </div>
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
            <div class="kpi-icon">📈</div>
            <div class="kpi-value">{{ $kpis['tasa_aprobacion'] }}%</div>
            <div class="kpi-label">Tasa de Aprobación</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">🏫</div>
            <div class="kpi-value">{{ $kpis['grupos_habilitados'] }}</div>
            <div class="kpi-label">Grupos Habilitados</div>
        </div>
    </div>

    @php $user = Auth::user(); @endphp
    @if($user->tienePrivilegio('reportes.ver'))
    <h2 class="section-title">Reportes — Gestión {{ $gestionCodigo }}</h2>
    <div style="background:#f8fafc; border:1.5px dashed #e2e8f0; border-radius:10px; padding:48px; text-align:center; color:#aaa;">
        <div style="font-size:36px; margin-bottom:12px;">📊</div>
        <p style="font-size:14px; font-weight:600; color:#5a5a5a;">Módulo de Reportes</p>
        <p style="font-size:13px; margin-top:6px;">Próximamente disponible.</p>
    </div>
    @endif

</div>
@endsection
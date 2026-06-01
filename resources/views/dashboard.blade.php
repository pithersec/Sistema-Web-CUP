@extends('layouts.app')

@section('title', 'Panel de Indicadores - CUP')
@section('page_title', 'Panel de Indicadores')

@section('content')
<style>
    .dashboard-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* GRID DE TARJETAS KPIs */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .kpi-card {
        background: white;
        border-radius: 10px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border-top: 4px solid #1a5fa8;
        transition: transform 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
    }

    .kpi-card:nth-child(2) {
        border-top-color: #27ae60;
    }

    .kpi-card:nth-child(3) {
        border-top-color: #c0392b;
    }

    .kpi-card:nth-child(4) {
        border-top-color: #f39c12;
    }

    .kpi-icon {
        font-size: 26px;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-family: 'Merriweather', serif;
        font-size: 34px;
        font-weight: 700;
        color: #0d3b6e;
    }

    .kpi-card:nth-child(2) .kpi-value {
        color: #27ae60;
    }

    .kpi-card:nth-child(3) .kpi-value {
        color: #c0392b;
    }

    .kpi-card:nth-child(4) .kpi-value {
        color: #f39c12;
    }

    .kpi-label {
        font-size: 11px;
        color: #5a5a5a;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 600;
        margin-top: 4px;
    }

    /* TABLA */
    .section-title {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 16px;
        margin-bottom: 16px;
        font-weight: 700;
    }

    .analytics-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    .custom-table thead {
        background: #0d3b6e;
        color: white;
    }

    .custom-table th {
        padding: 14px 20px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .custom-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    /* BADGES */
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-green {
        background: #d4f5e2;
        color: #1a7a3c;
    }

    .badge-red {
        background: #fde8e8;
        color: #c0392b;
    }

    .badge-blue {
        background: #dceeff;
        color: #1a5fa8;
    }
</style>

<div class="dashboard-wrapper">
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

    <h2 class="section-title">Resumen Estadístico por Carrera</h2>
    <div class="analytics-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Cupos Ofrecidos</th>
                    <th>Postulantes Registrados</th>
                    <th>Postulantes Aprobados</th>
                    <th>Estado de Disponibilidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumenCarreras as $carrera)
                <tr>
                    <td><strong>{{ $carrera->carrera_nombre }}</strong></td>
                    <td>{{ $carrera->total_cupos }}</td>
                    <td>{{ $carrera->total_postulantes }}</td>
                    <td>{{ $carrera->total_aprobados }}</td>
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
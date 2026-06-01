@extends('layouts.app')

@section('title', 'Panel de Indicadores - CU19')
@section('page_title', 'Panel de Indicadores')

@section('content')
<div style="width: 100%;">
    <div class="cards">
        <div class="card">
            <div class="card-icon">👥</div>
            <div class="card-value">{{ $kpis['total_inscritos'] }}</div>
            <div class="card-label">Total Inscritos</div>
        </div>
        <div class="card">
            <div class="card-icon">✅</div>
            <div class="card-value">{{ $kpis['total_aprobados'] }}</div>
            <div class="card-label">Total Aprobados</div>
        </div>
        <div class="card">
            <div class="card-icon">❌</div>
            <div class="card-value">{{ $kpis['total_reprobados'] }}</div>
            <div class="card-label">Total Reprobados</div>
        </div>
        <div class="card">
            <div class="card-icon">🏫</div>
            <div class="card-value">{{ $kpis['grupos_habilitados'] }}</div>
            <div class="card-label">Grupos Habilitados</div>
        </div>
    </div>

    <p class="section-title">Resumen por Carrera</p>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Cupos</th>
                    <th>Postulantes</th>
                    <th>Aprobados</th>
                    <th>Estado</th>
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
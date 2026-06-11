@extends('layouts.app')

@section('title', 'Detalle de Rendimiento - CUP')
@section('page_title', 'Detalle de Rendimiento')

@section('content')
<style>
    .detalle-wrapper { width: 100%; max-width: 900px; font-family: 'Source Sans 3', sans-serif; }
    .header-card { background: white; border-radius: 10px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .header-left { display: flex; align-items: center; gap: 16px; }
    .avatar { width: 56px; height: 56px; border-radius: 50%; background: #0d3b6e; color: white; display: flex; align-items: center; justify-content: center; font-family: 'Merriweather', serif; font-size: 20px; font-weight: 700; flex-shrink: 0; }
    .header-info h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 18px; margin-bottom: 4px; }
    .header-info p { color: #5a5a5a; font-size: 13px; }
    .btn-back { padding: 9px 18px; border: 1.5px solid #e2e8f0; border-radius: 6px; background: white; color: #5a5a5a; font-family: 'Source Sans 3', sans-serif; font-size: 13px; font-weight: 600; text-decoration: none; }
    .btn-back:hover { background: #f8fafc; }

    .section-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 16px; overflow: hidden; }
    .section-card-header { background: #0d3b6e; padding: 12px 20px; display: flex; align-items: center; gap: 10px; }
    .section-card-header h3 { color: white; font-family: 'Merriweather', serif; font-size: 13px; }
    .section-card-body { padding: 0; }

    .notas-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .notas-table thead { background: #0d3b6e; color: white; }
    .notas-table th { padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600; }
    .notas-table th.center { text-align: center; }
    .notas-table td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .notas-table tr:last-child td { border-bottom: none; }
    .notas-table tr:hover td { background: #f8fafc; }
    .notas-table td.center { text-align: center; }

    .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-green { background: #d4f5e2; color: #1a7a3c; }
    .badge-red   { background: #fde8e8; color: #c0392b; }
    .badge-gray  { background: #f1f5f9; color: #5a5a5a; }

    .info-box { background: #0d3b6e; padding: 12px 20px; font-size: 12px; color: white; border-bottom: 1px solid #1a5fa8; }

    @media (max-width: 768px) {
        .header-card { flex-direction: column; align-items: flex-start; }
        .notas-table { min-width: 500px; }
        .section-card-body { overflow-x: auto; }
    }
</style>

<div class="detalle-wrapper">

    {{-- HEADER --}}
    <div class="header-card">
        <div class="header-left">
            <div class="avatar">
                {{ strtoupper(substr($postulante->nombre ?? 'P', 0, 1)) }}
            </div>
            <div class="header-info">
                <h2>{{ $postulante->nombre }} {{ $postulante->apellido }}</h2>
                <p>
                    Código: <strong>{{ $postulante->codigo }}</strong>
                    &nbsp;·&nbsp; CI: {{ $postulante->ci }}
                    &nbsp;·&nbsp; Grupo: <strong>{{ $postulante->id_grupo }}</strong>
                    @if($grupo)
                        — Turno {{ ucfirst($grupo->nombre_turno) }} · Aula {{ $grupo->aula }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('rendimiento.index', ['gestion' => $postulante->gestion_grupo]) }}" class="btn-back">← Volver</a>
    </div>

    {{-- NOTAS --}}
    @if($examenes->isEmpty())
    <div style="background:white; border-radius:10px; padding:40px; text-align:center; color:#888; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="font-size:32px; margin-bottom:10px;">📭</div>
        <p>No hay notas registradas para este postulante.</p>
    </div>
    @else
    <div class="section-card">
        <div class="section-card-header">
            <span>📝</span>
            <h3>Notas por Materia</h3>
        </div>
        <div class="info-box">
            ℹ️ Cada examen muestra la nota obtenida y entre paréntesis la nota ponderada. Se aprueba la materia con Nota Final ≥ 60.
        </div>
        <div class="section-card-body">
            @php
                $primero = $examenes->first();
                $pond1 = (int)(($primero['e1']->ponderacion ?? 30));
                $pond2 = (int)(($primero['e2']->ponderacion ?? 30));
                $pond3 = (int)(($primero['e3']->ponderacion ?? 40));
            @endphp
            <table class="notas-table">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th class="center">Exam 1 ({{ $pond1 }}%)</th>
                        <th class="center">Exam 2 ({{ $pond2 }}%)</th>
                        <th class="center">Exam 3 ({{ $pond3 }}%)</th>
                        <th class="center">Nota Final</th>
                        <th class="center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($examenes as $e)
                    <tr>
                        <td><strong>{{ $e['materia'] }}</strong></td>
                        <td class="center">
                            @if($e['e1'])
                                {{ number_format($e['e1']->nota, 0) }}
                                <br><small style="color:#888;">({{ number_format($e['e1']->nota * ($e['e1']->ponderacion / 100), 1) }} pts)</small>
                            @else —
                            @endif
                        </td>
                        <td class="center">
                            @if($e['e2'])
                                {{ number_format($e['e2']->nota, 0) }}
                                <br><small style="color:#888;">({{ number_format($e['e2']->nota * ($e['e2']->ponderacion / 100), 1) }} pts)</small>
                            @else —
                            @endif
                        </td>
                        <td class="center">
                            @if($e['e3'])
                                {{ number_format($e['e3']->nota, 0) }}
                                <br><small style="color:#888;">({{ number_format($e['e3']->nota * ($e['e3']->ponderacion / 100), 1) }} pts)</small>
                            @else —
                            @endif
                        </td>
                        <td class="center">
                            @if($e['notaFinal'] !== null)
                                <strong>{{ $e['notaFinal'] }}</strong>
                            @else —
                            @endif
                        </td>
                        <td class="center">
                            @if($e['aprobado'] === null)
                                <span class="badge badge-gray">Sin notas</span>
                            @elseif($e['aprobado'])
                                <span class="badge badge-green">Aprobado</span>
                            @else
                                <span class="badge badge-red">Reprobado</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
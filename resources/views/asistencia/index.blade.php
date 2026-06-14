@extends('layouts.app')

@section('title', 'Gestión de Asistencia - CUP')
@section('page_title', 'Gestión de Asistencia')

@section('content')
<style>
    .asistencia-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }

    .toolbar-form {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        align-items: center;
        width: 100%;
    }

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

    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .table-header {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-header h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 15px;
        font-weight: 700;
    }

    .total-badge {
        background: #dceeff;
        color: #1a5fa8;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    /* DOS COLUMNAS */
    .planilla-dos-columnas {
        display: grid;
        grid-template-columns: 1fr 1px 1fr;
    }

    .planilla-divisoria { background: #e2e8f0; }

    .planilla-header {
        display: grid;
        grid-template-columns: 36px 80px 1fr 90px 80px;
        align-items: center;
        padding: 11px 16px;
        background: #0d3b6e;
        color: white;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .planilla-row {
        display: grid;
        grid-template-columns: 36px 80px 1fr 90px 80px;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        color: #333;
    }

    .planilla-row:hover { background: #f8fafc; }
    .planilla-row:last-child { border-bottom: none; }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-green { background: #d4f5e2; color: #1a7a3c; }
    .badge-red   { background: #fde8e8; color: #c0392b; }
    .badge-gray  { background: #e2e8f0; color: #5a5a5a; }

    .table-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
    }

    .btn-primary {
        padding: 10px 24px;
        background: #0d3b6e;
        color: white;
        border: none;
        border-radius: 6px;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary:hover { background: #1a5fa8; }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #888;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .toolbar-form { flex-wrap: wrap; }
        .filter-select { flex: 1; min-width: 0; font-size: 13px; padding: 8px 10px; }

        .planilla-dos-columnas { grid-template-columns: 1fr; }
        .planilla-divisoria { display: none; }
        /* Ocultar header duplicado en móvil */
        .planilla-dos-columnas > div:last-child > .planilla-header { display: none; }
    }
</style>

@if(session('success'))
<div style="background:#d4f5e2; color:#1a7a3c; padding:12px; border-radius:6px; margin-bottom:15px; font-size:13.5px; font-weight:600;">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fde8e8; color:#c0392b; padding:12px; border-radius:6px; margin-bottom:15px; font-size:13.5px; font-weight:600;">
    {{ session('error') }}
</div>
@endif

<div class="asistencia-wrapper">

    @php
        $gestionActual = $gestiones->firstWhere('codigo', $gestionCodigo);
        $fechaMin = $gestionActual?->fecha_ini
            ? \Carbon\Carbon::parse($gestionActual->fecha_ini)->toDateString()
            : now()->toDateString();
        $fechaMax = now()->toDateString();
    @endphp

    <form method="GET" action="{{ route('asistencia.index') }}" class="toolbar-form" id="filterForm">
        <select name="gestion" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            @foreach($gestiones as $g)
                <option value="{{ $g->codigo }}" {{ $g->codigo == $gestionCodigo ? 'selected' : '' }}>
                    Gestión {{ $g->codigo }}
                </option>
            @endforeach
        </select>

        <select name="id_grupo" class="filter-select" id="selectGrupo">
            <option value="">— Seleccionar Grupo / Materia —</option>
            @foreach($gruposMateria as $gm)
                <option value="{{ $gm->id_grupo }}"
                    data-gestion="{{ $gm->gestion_grupo }}"
                    data-materia="{{ $gm->id_materia }}"
                    {{ $grupoSeleccionado == $gm->id_grupo && $gestionSeleccionada == $gm->gestion_grupo && $materiaSeleccionada == $gm->id_materia ? 'selected' : '' }}>
                    Grupo {{ $gm->id_grupo }} — {{ $gm->materia->nombre ?? 'Materia' }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="codigo_gestion" id="codigo_gestion" value="{{ $gestionSeleccionada }}">
        <input type="hidden" name="id_materia" id="id_materia" value="{{ $materiaSeleccionada }}">

        <input type="date" name="fecha" class="filter-select" style="min-width:160px;"
            value="{{ $fecha }}"
            min="{{ $fechaMin }}"
            max="{{ $fechaMax }}"
            onchange="document.getElementById('filterForm').submit()">
    </form>

    <div class="table-card">
        <div class="table-header">
            <h2>
                Lista de Postulantes
                @if($grupoSeleccionado)
                    — Grupo {{ $grupoSeleccionado }} · {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                @endif
            </h2>
            @if($postulantes->count())
            <span class="total-badge">{{ $postulantes->count() }} postulantes</span>
            @endif
        </div>

        @if($grupoSeleccionado && $postulantes->count())
        <form method="POST" action="{{ route('asistencia.registrar') }}">
            @csrf
            <input type="hidden" name="id_grupo" value="{{ $grupoSeleccionado }}">
            <input type="hidden" name="codigo_gestion" value="{{ $gestionSeleccionada }}">
            <input type="hidden" name="id_materia" value="{{ $materiaSeleccionada }}">
            <input type="hidden" name="fecha" value="{{ $fecha }}">

            @php
                $total = $postulantes->count();
                $mitad = (int) ceil($total / 2);
                $columna1 = $postulantes->slice(0, $mitad);
                $columna2 = $postulantes->slice($mitad);
            @endphp

            @php $yaRegistrado = $asistencias->count() > 0; @endphp

            <div style="overflow-x:auto;">
            <div class="planilla-dos-columnas">

                {{-- Columna 1 --}}
                <div>
                    <div class="planilla-header">
                        <span>#</span><span>Código</span><span>Postulante</span><span style="text-align:center;">Presente</span><span>Estado</span>
                    </div>
                    @foreach($columna1 as $i => $p)
                    @php
                        $reg = $asistencias->firstWhere('codigo_postulante', $p->codigo);
                        $presente = $reg ? $reg->presente : false;
                    @endphp
                    <div class="planilla-row">
                        <span>{{ $i + 1 }}</span>
                        <span style="font-size:12px; color:#888;">{{ $p->codigo }}</span>
                        <span>{{ $p->datosPersonales->nombre ?? '' }} {{ $p->datosPersonales->apellido ?? '' }}</span>
                        <span style="text-align:center;">
                            <input type="checkbox"
                                name="asistencias[{{ $p->codigo }}]"
                                value="1"
                                {{ $presente ? 'checked disabled' : '' }}
                                style="width:16px; height:16px; {{ $presente ? 'cursor:not-allowed;' : 'cursor:pointer;' }}">
                        </span>
                        <span>
                            @if(!$reg)
                                <span class="badge badge-gray">Sin reg.</span>
                            @elseif($presente)
                                <span class="badge badge-green">Presente</span>
                            @else
                                <span class="badge badge-red">Ausente</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>

                <div class="planilla-divisoria"></div>

                {{-- Columna 2 --}}
                <div>
                    <div class="planilla-header">
                        <span>#</span><span>Código</span><span>Postulante</span><span style="text-align:center;">Presente</span><span>Estado</span>
                    </div>
                    @foreach($columna2 as $p)
                    @php
                        $reg = $asistencias->firstWhere('codigo_postulante', $p->codigo);
                        $presente = $reg ? $reg->presente : false;
                    @endphp
                    <div class="planilla-row">
                        <span>{{ $mitad + $loop->index + 1 }}</span>
                        <span style="font-size:12px; color:#888;">{{ $p->codigo }}</span>
                        <span>{{ $p->datosPersonales->nombre ?? '' }} {{ $p->datosPersonales->apellido ?? '' }}</span>
                        <span style="text-align:center;">
                            <input type="checkbox"
                                name="asistencias[{{ $p->codigo }}]"
                                value="1"
                                {{ $presente ? 'checked disabled' : '' }}
                                style="width:16px; height:16px; {{ $presente ? 'cursor:not-allowed;' : 'cursor:pointer;' }}">
                        </span>
                        <span>
                            @if(!$reg)
                                <span class="badge badge-gray">Sin reg.</span>
                            @elseif($presente)
                                <span class="badge badge-green">Presente</span>
                            @else
                                <span class="badge badge-red">Ausente</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>

            </div>
            </div>

            @php $todosPresentes = $postulantes->every(fn($p) => $asistencias->where('codigo_postulante', $p->codigo)->where('presente', true)->isNotEmpty()); @endphp

            @if($todosPresentes)
            <div class="table-footer">
                <span style="color:#1a7a3c; font-size:13px; font-weight:600;">✓ Todos los postulantes están marcados como presentes.</span>
            </div>
            @else
            <div class="table-footer">
                <button type="submit" class="btn-primary">💾 Guardar Asistencia</button>
            </div>
            @endif
        </form>

        @elseif($grupoSeleccionado)
            <div class="empty-state">No hay postulantes en este grupo.</div>
        @else
            <div class="empty-state">Selecciona un grupo y materia para registrar la asistencia.</div>
        @endif
    </div>
</div>

<script>
    document.getElementById('selectGrupo').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        document.getElementById('codigo_gestion').value = opt.dataset.gestion || '';
        document.getElementById('id_materia').value = opt.dataset.materia || '';
        document.getElementById('filterForm').submit();
    });
</script>
@endsection
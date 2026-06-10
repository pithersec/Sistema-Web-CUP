@extends('layouts.app')

@section('title', 'Registro de Exámenes - CUP')
@section('page_title', 'Registro de Exámenes')

@section('content')
<style>
    .planilla-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .planilla-header {
        display: grid;
        grid-template-columns: 40px 110px 1fr 100px 90px;
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
        grid-template-columns: 40px 110px 1fr 100px 90px;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        color: #333;
    }

    .planilla-row:hover { background: #f8fafc; }

    .nota-input {
        width: 70px;
        padding: 6px 10px;
        border: 1.5px solid #e2e8f0;
        border-radius: 4px;
        text-align: center;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 13px;
        outline: none;
    }

    .nota-input:focus { border-color: #2980b9; }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    /* Headers dobles — uno por columna del grid */
    .headers-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .planilla-dos-columnas {
        display: grid;
        grid-template-columns: 1fr 1px 1fr;
    }

    @media (max-width: 768px) {
        .planilla-grid { grid-template-columns: 1fr; }
        .headers-wrap { grid-template-columns: 1fr; }
        .headers-wrap .planilla-header:last-child { display: none; }

        .planilla-dos-columnas {
            grid-template-columns: 1fr;
        }

        .planilla-divisoria {
            display: none;
        }

        .planilla-dos-columnas > div:last-child > .planilla-header {
            display: none;
        }
    }
</style>

<div style="padding: 0; width: 100%;">

    @if(session('success'))
    <div style="padding: 12px; background: #d4f5e2; color: #1a7a3c; border-radius: 6px; margin-bottom: 16px; font-size: 13px; font-weight: 600; width: 100%;">
        {{ session('success') }}
    </div>
    @endif

    {{-- FILTROS --}}
    <div style="background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 20px 24px; margin-bottom: 20px;">
        <h3 style="font-size: 13px; font-weight: 600; color: #0d3b6e; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
            Seleccionar Planilla
        </h3>

        <form action="{{ route('notas.index') }}" method="GET"
            style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 14px; align-items: end;">

            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px;">Gestión</label>
                <select name="gestion" onchange="this.form.submit()"
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    @foreach($gestiones as $gest)
                    <option value="{{ $gest->codigo }}" {{ $gestionCodigo == $gest->codigo ? 'selected' : '' }}>
                        {{ $gest->codigo }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px;">Grupo</label>
                <select name="id_grupo" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="">-- Seleccionar Grupo --</option>
                    @foreach($grupos as $g)
                    <option value="{{ $g->id }}" {{ request('id_grupo')==$g->id ? 'selected' : '' }}>
                        {{ $g->id }} · {{ ucfirst($g->nombre_turno) }} · Aula {{ $g->aula }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px;">Materia</label>
                <select name="id_materia" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="">-- Seleccionar Materia --</option>
                    @foreach($materias as $m)
                    <option value="{{ $m->id }}" {{ request('id_materia')==$m->id ? 'selected' : '' }}>
                        {{ $m->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px;">Examen</label>
                <select name="nro_examen" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="1" {{ request('nro_examen')==1 ? 'selected' : '' }}>Examen 1 (30%)</option>
                    <option value="2" {{ request('nro_examen')==2 ? 'selected' : '' }}>Examen 2 (30%)</option>
                    <option value="3" {{ request('nro_examen')==3 ? 'selected' : '' }}>Examen 3 (40%)</option>
                </select>
            </div>

            <button type="submit"
                style="padding: 10px 20px; background: #1a5fa8; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                Mostrar Estudiantes
            </button>
        </form>
    </div>

    {{-- PLANILLA --}}
    <div style="background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
        <div style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px;">Planilla de Evaluación Actual</h2>
            @if($postulantes && count($postulantes) > 0)
            <span style="background: #dceeff; color: #1a5fa8; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                {{ count($postulantes) }} estudiantes en lista
            </span>
            @endif
        </div>

        <form action="{{ route('notas.registrar') }}" method="POST">
            @csrf
            <input type="hidden" name="id_grupo"   value="{{ request('id_grupo') }}">
            <input type="hidden" name="id_materia"  value="{{ request('id_materia') }}">
            <input type="hidden" name="nro_examen"  value="{{ request('nro_examen') }}">

            @if($postulantes && count($postulantes) > 0)

            @php
                $total = count($postulantes);
                $mitad = (int) ceil($total / 2);
                $columna1 = $postulantes->slice(0, $mitad);
                $columna2 = $postulantes->slice($mitad);
            @endphp

            <div class="planilla-dos-columnas">

            {{-- Columna 1 --}}
            <div>
                <div class="planilla-header">
                    <span>#</span><span>CI</span><span>Nombre</span><span>Nota (0-100)</span><span>Estado</span>
                </div>
                @foreach($columna1 as $index => $pos)
                <div class="planilla-row">
                    <span>{{ $index + 1 }}</span>
                    <span>{{ $pos->ci }}</span>
                    <span>{{ $pos->datosPersonales->nombre ?? '' }} {{ $pos->datosPersonales->apellido ?? '' }}</span>
                    <span><input type="number" name="notas[{{ $pos->codigo }}]" class="nota-input" step="1" min="0" max="100" value="{{ $pos->nota_actual !== null ? (int)$pos->nota_actual : '' }}" /></span>
                    <span>
                        @if($pos->nota_actual === null)
                            <span class="badge" style="background:#e2e8f0; color:#5a5a5a;">Pendiente</span>
                        @elseif($pos->nota_actual >= 60)
                            <span class="badge" style="background:#d4f5e2; color:#1a7a3c;">Aprobado</span>
                        @else
                            <span class="badge" style="background:#fde8e8; color:#c0392b;">Reprobado</span>
                        @endif
                    </span>
                </div>
                @endforeach
            </div>

            {{-- Línea divisoria --}}
            <div class="planilla-divisoria" style="background: #e2e8f0;"></div>

                {{-- Columna 2 --}}
                <div>
                    <div class="planilla-header">
                        <span>#</span><span>CI</span><span>Nombre</span><span>Nota (0-100)</span><span>Estado</span>
                    </div>
                    @foreach($columna2 as $index => $pos)
                    <div class="planilla-row">
                        <span>{{ $mitad + $loop->index + 1 }}</span>
                        <span>{{ $pos->ci }}</span>
                        <span>{{ $pos->datosPersonales->nombre ?? '' }} {{ $pos->datosPersonales->apellido ?? '' }}</span>
                        <span><input type="number" name="notas[{{ $pos->codigo }}]" class="nota-input" step="1" min="0" max="100" value="{{ $pos->nota_actual !== null ? (int)$pos->nota_actual : '' }}" /></span>
                        <span>
                            @if($pos->nota_actual === null)
                                <span class="badge" style="background:#e2e8f0; color:#5a5a5a;">Pendiente</span>
                            @elseif($pos->nota_actual >= 60)
                                <span class="badge" style="background:#d4f5e2; color:#1a7a3c;">Aprobado</span>
                            @else
                                <span class="badge" style="background:#fde8e8; color:#c0392b;">Reprobado</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>

            </div>

            @else
            <div style="text-align: center; padding: 30px; color: #5a5a5a; background: #f8fafc;">
                Por favor, seleccione los filtros de arriba y haga clic en "Cargar Estudiantes".
            </div>
            @endif

            <div style="display: flex; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                <button type="submit"
                    style="padding: 11px 28px; border: none; border-radius: 6px; background: #0d3b6e; color: white; font-size: 14px; font-weight: 600; cursor: pointer;">
                    💾 Guardar Notas de la Planilla
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
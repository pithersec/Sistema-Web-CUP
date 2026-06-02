@extends('layouts.app')

@section('title', 'Gestión de Carreras y Cupos - CUP')
@section('page_title', 'Gestión de Carreras y Cupos')

@section('content')
<style>
    .carreras-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* SELECTOR DE GESTION */
    .gestion-selector {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        padding: 18px 24px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .gestion-selector label {
        font-size: 12px;
        font-weight: 600;
        color: #0d3b6e;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        white-space: nowrap;
    }

    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        background: white;
        color: #333;
    }

    /* CARDS & TABLES */
    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .custom-table thead {
        background: #0d3b6e;
        color: white;
    }

    .custom-table th {
        padding: 11px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .custom-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
        vertical-align: middle;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    /* COMPONENTES DINÁMICOS DE CUPOS */
    .cupos-bar {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bar-bg {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        min-width: 100px;
    }

    .bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    .bar-green {
        background: #27ae60;
    }

    .bar-yellow {
        background: #f39c12;
    }

    .bar-red {
        background: #c0392b;
    }

    .bar-label {
        font-size: 12px;
        color: #5a5a5a;
        white-space: nowrap;
    }

    .cupos-input {
        width: 70px;
        padding: 6px 10px;
        border: 1.5px solid #e2e8f0;
        border-radius: 4px;
        font-size: 13px;
        text-align: center;
        font-family: 'Source Sans 3', sans-serif;
        outline: none;
    }

    .cupos-input:focus {
        border-color: #2980b9;
        box-shadow: 0 0 4px rgba(41, 128, 185, 0.2);
    }

    /* BADGES */
    .badge {
        display: inline-block;
        padding: 3px 10px;
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

    .badge-yellow {
        background: #fef9e7;
        color: #d68910;
    }

    /* BOTONES */
    .actions {
        display: flex;
        gap: 6px;
    }

    .btn-action {
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-save {
        background: #d4f5e2;
        color: #1a7a3c;
    }

    .btn-save:hover {
        background: #c2f0d5;
    }

    .footer-buttons {
        display: flex;
        justify-content: flex-end;
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
    }

    .btn-primary {
        padding: 11px 28px;
        border: none;
        border-radius: 6px;
        background: #0d3b6e;
        color: white;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-primary:hover {
        background: #1a5fa8;
    }
</style>

@if(session('success'))
<div
    style="background: #d4f5e2; color: #1a7a3c; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13.5px; font-weight: 600;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div
    style="background: #fde8e8; color: #c0392b; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13.5px; font-weight: 600;">
    {{ session('error') }}
</div>
@endif

<div class="carreras-wrapper">
    <div class="gestion-selector">
        <label for="codigo_gestion_select">Gestión Académica:</label>
        <form action="{{ route('carreras.index') }}" method="GET" id="gestionForm">
            <select name="codigo_gestion" id="codigo_gestion_select" class="filter-select"
                onchange="document.getElementById('gestionForm').submit();">
                @foreach($gestiones as $g)
                <option value="{{ $g->codigo }}" {{ $gestion_seleccionada==$g->codigo ? 'selected' : '' }}>
                    Gestión {{ $g->codigo }} </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2>Carreras y Cupos · Gestión {{ $gestion_seleccionada }}</h2>
        </div>

        <form action="{{ route('carreras.guardarMasivo') }}" method="POST" id="masivoForm">
            @csrf
            <input type="hidden" name="codigo_gestion" value="{{ $gestion_seleccionada }}">

            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Carrera</th>
                        <th>Modalidad</th>
                        <th>Cupos Asignados</th>
                        <th>Ocupación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carreras as $c)
                    @php
                    // Lógica para cálculo de barras y badges dinámicos
                    $porcentaje = 0;
                    if ($c->cupos > 0) {
                    $porcentaje = min(100, round(($c->ocupados / $c->cupos) * 100));
                    }

                    // Determinación de colores según saturación de cupos
                    $colorClass = 'bar-green';
                    $badgeClass = 'badge-green';
                    $badgeText = 'Disponible';

                    if ($porcentaje >= 100) {
                    $colorClass = 'bar-red';
                    $badgeClass = 'badge-red';
                    $badgeText = 'Lleno';
                    } elseif ($porcentaje >= 70) {
                    $colorClass = 'bar-yellow';
                    $badgeClass = 'badge-yellow';
                    $badgeText = $porcentaje . '% lleno';
                    }
                    @endphp
                    <tr>
                        <td><strong>{{ $c->codigo }}</strong></td>
                        <td>{{ $c->nombre }}</td>
                        <td>{{ $c->modalidad }}</td>
                        <td>
                            <input type="number" name="cupos[{{ $c->codigo }}]"
                                class="cupos-input row-input-{{ $c->codigo }}" value="{{ $c->cupos }}" min="0" />
                        </td>
                        <td>
                            <div class="cupos-bar">
                                <div class="bar-bg">
                                    <div class="bar-fill {{ $colorClass }}" style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <span class="bar-label">{{ $c->ocupados }}/{{ $c->cupos }}</span>
                            </div>
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
                        <td>
                            <div class="actions">
                                <button type="button" class="btn-action btn-save"
                                    onclick="guardarFilaIndividual('{{ $c->codigo }}')">
                                    Guardar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #888;">
                            No existen carreras registradas en la base de datos.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if(count($carreras) > 0)
            <div class="footer-buttons">
                <button type="submit" class="btn-primary">💾 Guardar Todos los Cambios</button>
            </div>
            @endif
        </form>
    </div>
</div>

<form id="individualRowForm" action="{{ route('carreras.guardarFila') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="codigo_carrera" id="ind_codigo_carrera">
    <input type="hidden" name="codigo_gestion" id="ind_codigo_gestion" value="{{ $gestion_seleccionada }}">
    <input type="hidden" name="cupos" id="ind_cupos">
</form>

<script>
    // Sincroniza el input de la fila elegida hacia el formulario auxiliar y lo envía
    function guardarFilaIndividual(codigoCarrera) {
        const inputValor = document.querySelector('.row-input-' + codigoCarrera).value;
        
        document.getElementById('ind_codigo_carrera').value = codigoCarrera;
        document.getElementById('ind_cupos').value = inputValor;
        
        if(confirm('¿Desea actualizar los cupos individuales para esta carrera?')) {
            document.getElementById('individualRowForm').submit();
        }
    }
</script>
@endsection
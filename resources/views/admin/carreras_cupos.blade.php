@extends('layouts.app')

@section('title', 'Gestión de Carreras y Cupos - FICCT')
@section('page_title', 'Gestión de Carreras y Cupos')

@section('content')
<div style="width: 100%;">

    @if(session('success'))
    <div
        style="padding: 12px; background: #d4f5e2; color: #1a7a3c; border-radius: 6px; margin-bottom: 16px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div
        style="padding: 12px; background: #fde8e8; color: #c0392b; border-radius: 6px; margin-bottom: 16px; font-size: 13px; font-weight: 600;">
        {{ session('error') }}
    </div>
    @endif

    <div class="gestion-selector">
        <label for="select-gestion">Gestión Académica:</label>
        <form action="{{ url('/admin/carreras-cupos') }}" method="GET" id="form-cambiar-gestion"
            style="display: inline-flex; width: 100%;">
            <select name="codigo_gestion" id="select-gestion"
                onchange="document.getElementById('form-cambiar-gestion').submit();"
                style="width: 100%; max-width: 400px;">
                @foreach($gestiones as $g)
                <option value="{{ $g->codigo }}" {{ $gestion_selected=($gestion_seleccionada==$g->codigo) ? 'selected' :
                    '' }}>
                    Gestión {{ $g->año }} · Periodo {{ $g->periodo }}
                </option>
                @endforeach
            </select>
        </form>
    </div>

    <form action="{{ url('/admin/carreras-cupos/guardar-masivo') }}" method="POST">
        @csrf
        <input type="hidden" name="codigo_gestion" value="{{ $gestion_seleccionada }}">

        <div class="table-card">
            <div class="table-header">
                <h2>Carreras y Cupos · Periodo Activo</h2>
            </div>

            <table>
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
                    @forelse($carreras as $carrera)
                    @php
                    // Cálculos lógicos de ocupación porcentual para componentes gráficos
                    $porcentaje = 0;
                    if ($carrera->cupos > 0) {
                    $porcentaje = min(100, round(($carrera->ocupados / $carrera->cupos) * 100));
                    }

                    // Determinación de colores del CSS basado en el porcentaje
                    $clase_barra = 'bar-green';
                    $clase_badge = 'badge-green';
                    $texto_badge = 'Disponible';

                    if ($porcentaje >= 100) {
                    $clase_barra = 'bar-red';
                    $clase_badge = 'badge-red';
                    $texto_badge = 'Lleno';
                    } elseif ($porcentaje >= 70) {
                    $clase_barra = 'bar-yellow';
                    $clase_badge = 'badge-yellow';
                    $texto_badge = $porcentaje . '% lleno';
                    }
                    @endphp
                    <tr>
                        <td><strong>{{ $carrera->codigo }}</strong></td>
                        <td>{{ $carrera->nombre }}</td>
                        <td>{{ $carrera->modalidad }}</td>

                        <td>
                            <input type="number" name="cupos[{{ $carrera->codigo }}]" class="cupos-input"
                                value="{{ $carrera->cupos }}" min="0" id="input-{{ $carrera->codigo }}" />
                        </td>

                        <td>
                            <div class="cupos-bar">
                                <div class="bar-bg">
                                    <div class="bar-fill {{ $clase_barra }}" style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <span class="bar-label">{{ $carrera->ocupados }}/{{ $carrera->cupos }}</span>
                            </div>
                        </td>

                        <td>
                            <span class="badge {{ $clase_badge }}">{{ $texto_badge }}</span>
                        </td>

                        <td>
                            <div class="actions">
                                <button type="button" class="btn-action btn-save"
                                    onclick="guardarFilaIndividual('{{ $carrera->codigo }}')">
                                    Guardar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: var(--gris);">No hay carreras
                            configuradas en el sistema.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="footer-buttons">
                <button type="submit" class="btn-primary">💾 Guardar Cambios</button>
            </div>
        </div>
    </form>
</div>

<form id="form-fila-individual" action="{{ url('/admin/carreras-cupos/guardar-fila') }}" method="POST"
    style="display: none;">
    @csrf
    <input type="hidden" name="codigo_gestion" value="{{ $gestion_seleccionada }}">
    <input type="hidden" name="codigo_carrera" id="hidden-codigo-carrera">
    <input type="hidden" name="cupos" id="hidden-cupos">
</form>

<script>
    /**
     * Captura dinámicamente el valor del input de la fila elegida
     * y gatilla el submit del formulario oculto unitario.
     */
    function guardarFilaIndividual(codigoCarrera) {
        var inputVal = document.getElementById('input-' + codigoCarrera).value;
        document.getElementById('hidden-codigo-carrera').value = codigoCarrera;
        document.getElementById('hidden-cupos').value = inputVal;
        document.getElementById('form-fila-individual').submit();
    }
</script>
@endsection
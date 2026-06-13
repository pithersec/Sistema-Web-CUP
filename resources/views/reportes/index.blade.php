@extends('layouts.app')

@section('title', 'Reportes - CUP')
@section('page_title', 'Generar Reportes')

@section('content')
<style>
    .reportes-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* SELECTOR DE TIPO */
    .tipos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }
    .tipo-card {
        border: 2px solid #e0e8f0;
        border-radius: 8px;
        padding: 12px 14px;
        cursor: pointer;
        transition: all .15s;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #333;
        background: white;
    }
    .tipo-card:hover { border-color: var(--azul-claro); background: #f0f5fc; }
    .tipo-card.selected { border-color: var(--azul); background: #e8f0fb; font-weight: 600; color: var(--azul); }
    .tipo-card .ico { font-size: 18px; }

    /* TOOLBAR FILTROS — igual a postulantes */
    .toolbar-form {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        align-items: center;
        width: 100%;
        flex-wrap: wrap;
    }
    .search-box {
        flex: 1;
        position: relative;
        min-width: 160px;
    }
    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        background: white;
        color: #333;
        min-width: 160px;
        cursor: pointer;
        font-family: 'Source Sans 3', sans-serif;
    }
    .filter-select:focus { border-color: var(--azul-claro); }

    .btn-group { display: flex; gap: 8px; align-items: center; }
    .btn {
        padding: 9px 18px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Source Sans 3', sans-serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background .15s, transform .1s;
        text-decoration: none;
    }
    .btn:active { transform: scale(0.97); }
    .btn-primary { background: var(--azul); color: white; }
    .btn-primary:hover { background: var(--azul-claro); }
    .btn-pdf  { background: var(--rojo); color: white; }
    .btn-pdf:hover  { background: var(--rojo-claro); }
    .btn-excel { background: #1a7431; color: white; }
    .btn-excel:hover { background: #24a046; }

    /* TABLA — igual a postulantes */
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
        color: var(--azul);
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
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }
    .custom-table thead { background: var(--azul); color: white; }
    .custom-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
    }
    .custom-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
        vertical-align: middle;
    }
    .custom-table tr:last-child td { border-bottom: none; }
    .custom-table tr:hover td { background: #f8fafc; }

    .pagination-box {
        padding: 14px 24px;
        border-top: 1px solid #e2e8f0;
        background: white;
    }
    .pagination-box p { display: none; }
    .pagination-box nav > div:first-child { display: none; }
    .pagination-box nav {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .pagination-box span,
    .pagination-box a {
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 4px;
        color: #1a5fa8;
        text-decoration: none;
    }
    .pagination-box a:hover { background: #dceeff; }

    .pagination-box svg {
        width: 14px;
        height: 14px;
    }

    .sin-datos {
        text-align: center;
        padding: 40px;
        color: #888;
        font-style: italic;
    }

    .filtro-condicional { display: none; }
    .filtro-condicional.visible { display: flex; }
    .flex-col { flex-direction: column; gap: 4px; }
    .filter-label { font-size: 11px; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: 0.4px; }

    /* Sección tipo: card con título */
    .section-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 20px 24px;
        margin-bottom: 16px;
    }
    .section-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--azul);
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 14px;
    }
</style>

<div class="reportes-wrapper">

    {{-- TIPO DE REPORTE --}}
    <div class="section-card">
        <div class="section-label">Tipo de Reporte</div>
        <div class="tipos-grid">
            @php
            $tipos = [
                'postulantes'          => ['📋', 'Lista de Postulantes'],
                'aprobados'            => ['✅', 'Aprobados por Carrera'],
                'reprobados'           => ['❌', 'Lista de Reprobados'],
                'estadisticas_materia' => ['📈', 'Estadísticas por Materia'],
                'docentes_grupo'       => ['👨‍🏫', 'Docentes por Grupo'],
                'grupos_aprobados'     => ['🏆', 'Grupos con Más Aprobados'],
                'recaudacion'          => ['💰', 'Registro de Pagos'],
                'promedios_generales'  => ['📉', 'Promedios Generales'],
                'grupos_habilitados'   => ['📅', 'Grupos Habilitados por Gestión'],
                'asistencia'           => ['📝', 'Lista de Asistencia'],
            ];
            @endphp
            @foreach($tipos as $val => [$ico, $label])
            <div class="tipo-card {{ $tipoActual === $val ? 'selected' : '' }}"
                    data-value="{{ $val }}"
                    onclick="seleccionarTipo('{{ $val }}')">
                <span class="ico">{{ $ico }}</span>
                <span>{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- FILTROS --}}
    <form action="{{ route('reportes.index') }}" method="GET" id="form-filtros">
        <input type="hidden" name="tipo_reporte" id="tipo_reporte" value="{{ $tipoActual }}">

        <div class="toolbar-form">

            {{-- Gestión --}}
            <div class="filtro-condicional flex-col" id="filtro-gestion">
                <span class="filter-label">Gestión</span>
                <select name="gestion" class="filter-select" onchange="submitFiltros()">
                    <option value="">-- Todas --</option>
                    @foreach($gestiones as $g)
                    <option value="{{ $g->codigo }}" {{ $gestion === $g->codigo ? 'selected' : '' }}>
                        {{ $g->codigo }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Carrera --}}
            <div class="filtro-condicional flex-col" id="filtro-carrera">
                <span class="filter-label">Carrera</span>
                <select name="carrera" class="filter-select" onchange="submitFiltros()">
                    <option value="">-- Todas --</option>
                    @foreach($carreras as $c)
                    <option value="{{ $c->codigo }}|{{ $c->plan }}|{{ $c->modalidad }}"
                        {{ $carrera === $c->codigo.'|'.$c->plan.'|'.$c->modalidad ? 'selected' : '' }}>
                        {{ $c->nombre }}{{ $c->modalidad === 'virtual' ? ' (Virtual)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Estado --}}
            <div class="filtro-condicional flex-col" id="filtro-estado">
                <span class="filter-label">Estado</span>
                <select name="estado" class="filter-select" onchange="submitFiltros()">
                    <option value="">-- Todos --</option>
                    <option value="aprobado"    {{ $estado === 'aprobado'    ? 'selected' : '' }}>Aprobado</option>
                    <option value="reprobado"   {{ $estado === 'reprobado'   ? 'selected' : '' }}>Reprobado</option>
                    <option value="inscrito"    {{ $estado === 'inscrito'    ? 'selected' : '' }}>Inscrito</option>
                    <option value="preinscrito" {{ $estado === 'preinscrito' ? 'selected' : '' }}>Preinscrito</option>
                    <option value="baja"        {{ $estado === 'baja'        ? 'selected' : '' }}>Baja</option>
                </select>
            </div>

            {{-- Turno --}}
            <div class="filtro-condicional flex-col" id="filtro-turno">
                <span class="filter-label">Turno</span>
                <select name="turno" class="filter-select" onchange="submitFiltros()">
                    <option value="">-- Todos --</option>
                    @foreach($turnos as $t)
                    <option value="{{ $t->nombre }}" {{ $turno === $t->nombre ? 'selected' : '' }}>
                        {{ ucfirst($t->nombre) }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Materia --}}
            <div class="filtro-condicional flex-col" id="filtro-materia">
                <span class="filter-label">Materia</span>
                <select name="materia" class="filter-select" onchange="submitFiltros()">
                    <option value="">-- Todas --</option>
                    @foreach($materias as $m)
                    <option value="{{ $m->id }}" {{ $materia == $m->id ? 'selected' : '' }}>
                        {{ $m->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Fecha inicio --}}
            <div class="filtro-condicional flex-col" id="filtro-fecha-ini">
                <span class="filter-label">Fecha Inicio</span>
                <input type="date" name="fecha_inicio" class="filter-select"
                        value="{{ $fechaInicio }}" onchange="submitFiltros()">
            </div>

            {{-- Fecha fin --}}
            <div class="filtro-condicional flex-col" id="filtro-fecha-fin">
                <span class="filter-label">Fecha Fin</span>
                <input type="date" name="fecha_fin" class="filter-select"
                        value="{{ $fechaFin }}" onchange="submitFiltros()">
            </div>

            {{-- Botones exportar (solo si hay tipo seleccionado y hay datos) --}}
            @if($tipoActual && $filas->isNotEmpty())
            <div class="btn-group" style="margin-left: auto; align-self: flex-end;">
                <a href="{{ route('reportes.exportar', array_merge(request()->query(), ['formato' => 'pdf'])) }}"
                    class="btn btn-pdf" target="_blank">📄 PDF</a>
                <a href="{{ route('reportes.exportar', array_merge(request()->query(), ['formato' => 'excel'])) }}"
                    class="btn btn-excel">📊 Excel</a>
            </div>
            @endif

        </div>
    </form>

    {{-- TABLA DE RESULTADOS --}}
    @if($tipoActual && $filas->isNotEmpty())
    <div class="table-card" id="tabla-resultados">
        <div class="table-header">
            <h2>{{ $titulo }}</h2>
            <span class="total-badge">{{ $totalFilas }} registros</span>
        </div>
        @if($resumen)
        <div style="padding: 8px 24px; font-size: 13px; color: #555; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            {{ $resumen }}
        </div>
        @endif
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        @foreach($columnas as $col)
                        <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($filas as $fila)
                    <tr>
                        @foreach((array)$fila as $valor)
                        <td>{{ $valor }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-box">
            {{ $filas->withQueryString()->links() }}
        </div>
    </div>
    @elseif($tipoActual && $filas->isEmpty())
    <div class="table-card">
        <p class="sin-datos">No hay datos disponibles para generar el reporte con los filtros seleccionados.</p>
    </div>
    @endif

</div>

<script>
const filtrosPorTipo = {
    postulantes:          ['filtro-gestion', 'filtro-carrera', 'filtro-estado'],
    aprobados:            ['filtro-gestion', 'filtro-carrera'],
    reprobados:           ['filtro-gestion', 'filtro-carrera'],
    promedios_materia:    ['filtro-gestion'],
    estadisticas_materia: ['filtro-gestion'],
    docentes_grupo:       ['filtro-gestion', 'filtro-turno'],
    grupos_aprobados:     ['filtro-gestion', 'filtro-turno'],
    recaudacion:          ['filtro-fecha-ini', 'filtro-fecha-fin'],
    promedios_generales:  ['filtro-gestion', 'filtro-estado'],
    grupos_habilitados:   ['filtro-gestion', 'filtro-turno'],
    asistencia:           ['filtro-gestion', 'filtro-materia', 'filtro-turno'],
};
const todosLosFiltros = ['filtro-gestion','filtro-carrera','filtro-turno','filtro-materia','filtro-fecha-ini','filtro-fecha-fin', 'filtro-estado'];

function mostrarFiltros(tipo) {
    todosLosFiltros.forEach(id => {
        document.getElementById(id).classList.remove('visible');
    });
    (filtrosPorTipo[tipo] || []).forEach(id => {
        document.getElementById(id).classList.add('visible');
    });
}

function seleccionarTipo(valor) {
    // Si es el mismo tipo, no hacer nada
    if (document.getElementById('tipo_reporte').value === valor) return;

    document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`.tipo-card[data-value="${valor}"]`).classList.add('selected');
    document.getElementById('tipo_reporte').value = valor;

    // Ocultar tabla anterior
    const tabla = document.getElementById('tabla-resultados');
    if (tabla) tabla.style.display = 'none';

    mostrarFiltros(valor);

    // Submit para cargar datos del nuevo tipo
    document.getElementById('form-filtros').submit();
}

function submitFiltros() {
    document.getElementById('form-filtros').submit();
}

// Inicializar filtros visibles según tipo actual
const tipoActual = '{{ $tipoActual }}';
if (tipoActual) mostrarFiltros(tipoActual);
</script>
@endsection
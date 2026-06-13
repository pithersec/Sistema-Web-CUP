@extends('layouts.app')

@section('title', 'Reportes - CUP')
@section('page_title', 'Generar Reportes')

@section('content')
<style>
    .reportes-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }

    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 20px 24px;
        margin-bottom: 20px;
    }
    .card h3 {
        font-size: 12px;
        font-weight: 600;
        color: var(--azul);
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 16px;
    }

    .form-group select,
    .form-group input {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Source Sans 3', sans-serif;
        background: white;
        color: #374151;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230d3b6e' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 28px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: border-color .15s, box-shadow .15s;
    }
    .form-group input {
        background-image: none;
        padding-right: 12px;
    }
    .form-group select:focus,
    .form-group input:focus {
        outline: none;
        border-color: var(--azul-claro);
        box-shadow: 0 0 0 3px rgba(26,95,168,0.15);
    }

    /* Filtros condicionales */
    .filtro-condicional { display: none; }
    .filtro-condicional.visible { display: flex; }

    .btn {
        padding: 9px 22px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Source Sans 3', sans-serif;
        letter-spacing: 0.3px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background .15s, transform .1s;
    }
    .btn:active { transform: scale(0.97); }
    .btn-pdf  { background: var(--rojo); color: white; }
    .btn-pdf:hover  { background: var(--rojo-claro); }
    .btn-excel { background: #1a7431; color: white; }
    .btn-excel:hover { background: #24a046; }

    .tipos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 10px;
        margin-bottom: 4px;
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
    }
    .tipo-card:hover { border-color: var(--azul-claro); background: #f0f5fc; }
    .tipo-card.selected { border-color: var(--azul); background: #e8f0fb; font-weight: 600; color: var(--azul); }
    .tipo-card .ico { font-size: 18px; }
    input[name="tipo_reporte"] { display: none; }
</style>

<div class="reportes-wrapper">

    {{-- SELECTOR DE TIPO --}}
    <div class="card">
        <h3>Tipo de Reporte</h3>
        <div class="tipos-grid" id="tipos-grid">
            @php
            $tipos = [
                'postulantes'          => ['📋', 'Lista de Postulantes'],
                'aprobados'            => ['✅', 'Aprobados por Carrera'],
                'reprobados'           => ['❌', 'Lista de Reprobados'],
                'promedios_materia'    => ['📊', 'Promedios por Materia'],
                'estadisticas_materia' => ['📈', 'Estadísticas por Materia'],
                'docentes_grupo'       => ['👨‍🏫', 'Docentes por Grupo'],
                'grupos_aprobados'     => ['🏆', 'Grupos con Más Aprobados'],
                'recaudacion'          => ['💰', 'Recaudación por Pagos'],
                'promedios_generales'  => ['📉', 'Promedios Generales'],
                'grupos_habilitados'   => ['📅', 'Grupos Habilitados por Gestión'],
                'asistencia'           => ['📝', 'Lista de Asistencia'],
            ];
            @endphp
            @foreach($tipos as $val => [$ico, $label])
            <div class="tipo-card" data-value="{{ $val }}" onclick="seleccionarTipo('{{ $val }}')">
                <span class="ico">{{ $ico }}</span>
                <span>{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- FORMULARIO DE FILTROS --}}
    <div class="card">
        <h3>Filtros y Exportación</h3>
        <form method="POST" action="{{ route('reportes.generar') }}" id="form-reporte">
            @csrf
            <input type="hidden" name="tipo_reporte" id="tipo_reporte" value="">

            <div class="form-grid">
                {{-- Gestión (casi todos) --}}
                <div class="form-group filtro-condicional visible" id="filtro-gestion">
                    <label>Gestión</label>
                    <select name="gestion">
                        <option value="">-- Todas --</option>
                        @foreach($gestiones as $g)
                        <option value="{{ $g->codigo }}">{{ $g->codigo }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Carrera --}}
                <div class="form-group filtro-condicional" id="filtro-carrera">
                    <label>Carrera</label>
                    <select name="carrera">
                        <option value="">-- Todas --</option>
                        @foreach($carreras->groupBy('modalidad') as $modalidad => $grupo)
                        <optgroup label="{{ ucfirst($modalidad) }}">
                            @foreach($grupo as $c)
                            <option value="{{ $c->codigo }}">{{ $c->nombre }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Turno --}}
                <div class="form-group filtro-condicional" id="filtro-turno">
                    <label>Turno</label>
                    <select name="turno">
                        <option value="">-- Todos --</option>
                        @foreach($turnos as $t)
                        <option value="{{ $t->nombre }}">{{ ucfirst($t->nombre) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Materia --}}
                <div class="form-group filtro-condicional" id="filtro-materia">
                    <label>Materia</label>
                    <select name="materia">
                        <option value="">-- Todas --</option>
                        @foreach($materias as $m)
                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Rango fechas (recaudación) --}}
                <div class="form-group filtro-condicional" id="filtro-fecha-ini">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio">
                </div>
                <div class="form-group filtro-condicional" id="filtro-fecha-fin">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin">
                </div>

                {{-- Botones --}}
                <div class="btn-group">
                    <button type="submit" name="formato" value="pdf" class="btn btn-pdf">
                        📄 PDF
                    </button>
                    <button type="submit" name="formato" value="excel" class="btn btn-excel">
                        📊 Excel
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
const filtrosPorTipo = {
    postulantes:          ['filtro-gestion', 'filtro-carrera'],
    aprobados:            ['filtro-gestion', 'filtro-carrera'],
    reprobados:           ['filtro-gestion', 'filtro-carrera'],
    promedios_materia:    ['filtro-gestion'],
    estadisticas_materia: ['filtro-gestion'],
    docentes_grupo:       ['filtro-gestion', 'filtro-turno'],
    grupos_aprobados:     ['filtro-gestion'],
    recaudacion:          ['filtro-fecha-ini', 'filtro-fecha-fin'],
    promedios_generales:  ['filtro-gestion', 'filtro-carrera'],
    grupos_habilitados:   ['filtro-gestion', 'filtro-turno'],
    asistencia:           ['filtro-gestion', 'filtro-materia', 'filtro-turno'],
};

const todosLosFiltros = ['filtro-gestion','filtro-carrera','filtro-turno','filtro-materia','filtro-fecha-ini','filtro-fecha-fin'];

function seleccionarTipo(valor) {
    // marcar tarjeta
    document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`.tipo-card[data-value="${valor}"]`).classList.add('selected');
    document.getElementById('tipo_reporte').value = valor;

    // mostrar filtros correspondientes
    todosLosFiltros.forEach(id => {
        const el = document.getElementById(id);
        el.classList.remove('visible');
    });
    (filtrosPorTipo[valor] || []).forEach(id => {
        document.getElementById(id).classList.add('visible');
    });
}

// Validar antes de enviar
document.getElementById('form-reporte').addEventListener('submit', function(e) {
    if (!document.getElementById('tipo_reporte').value) {
        e.preventDefault();
        alert('Por favor selecciona un tipo de reporte.');
    }
});
</script>
@endsection

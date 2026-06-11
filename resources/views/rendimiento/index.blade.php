@extends('layouts.app')

@section('title', 'Rendimiento Académico - CUP')
@section('page_title', 'Rendimiento Académico')

@section('content')
<style>
    .rendimiento-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }
    .filtros-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 20px 24px; margin-bottom: 20px; }
    .filtros-card h3 { font-size: 13px; font-weight: 600; color: #0d3b6e; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
    .filtros-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 14px; align-items: end; }
    .filtro-group label { display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; }
    .filtro-group select { width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none; color: #333; }
    .filtro-group select:focus { border-color: #1a5fa8; }
    .btn-filtrar { padding: 10px 20px; background: #1a5fa8; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap; }
    .btn-filtrar:hover { background: #0d3b6e; }

    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .table-header { padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; }
    .table-header h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px; }
    .total-badge { background: #dceeff; color: #1a5fa8; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }

    .table-scroll { overflow-x: auto; }
    .custom-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 700px; }
    .custom-table thead { background: #0d3b6e; color: white; }
    .custom-table th { padding: 11px 14px; text-align: left; font-weight: 600; font-size: 12px; letter-spacing: 0.5px; white-space: nowrap; }
    .custom-table td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #333; vertical-align: middle; }
    .custom-table tr:last-child td { border-bottom: none; }
    .custom-table tr:hover td { background: #f8fafc; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-green  { background: #d4f5e2; color: #1a7a3c; }
    .badge-red    { background: #fde8e8; color: #c0392b; }
    .badge-yellow { background: #fef9e7; color: #d68910; }
    .badge-blue   { background: #dceeff; color: #1a5fa8; }
    .badge-dark   { background: #e2e2e2; color: #1a1a1a; }
    .badge-gray   { background: #f1f5f9; color: #5a5a5a; }

    .nota-cell { text-align: center; }
    .nota-cell .nota-val { font-weight: 600; }
    .nota-cell .nota-estado { font-size: 10px; margin-top: 2px; }

    .pagination-box { padding: 14px 24px; border-top: 1px solid #e2e8f0; background: white; }
    .pagination-box svg { width: 14px; height: 14px; }
    .pagination-box nav { display: flex; align-items: center; gap: 4px; }
    .pagination-box span,
    .pagination-box a { font-size: 13px; padding: 4px 10px; border-radius: 4px; color: #1a5fa8; text-decoration: none; }
    .pagination-box a:hover { background: #dceeff; }
    .pagination-box p { display: none; }
    .pagination-box nav > div:first-child { display: none; }

    @media (max-width: 768px) {
        .filtros-grid { grid-template-columns: 1fr 1fr; }
        .filtros-grid button { grid-column: 1 / -1; }
    }
</style>

<div class="rendimiento-wrapper">

    {{-- FILTROS --}}
    <div class="filtros-card">
        <h3>Filtros</h3>
        <form action="{{ route('rendimiento.index') }}" method="GET" class="filtros-grid">

            <div class="filtro-group">
                <label>Gestión</label>
                <select name="gestion" onchange="this.form.submit()">
                    @foreach($gestiones as $g)
                    <option value="{{ $g->codigo }}" {{ $gestionCodigo == $g->codigo ? 'selected' : '' }}>
                        {{ $g->codigo }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filtro-group">
                <label>Grupo</label>
                <select name="id_grupo">
                    <option value="">Todos los grupos</option>
                    @foreach($grupos as $g)
                    <option value="{{ $g->id }}" {{ $idGrupo == $g->id ? 'selected' : '' }}>
                        {{ $g->id }} · {{ ucfirst($g->nombre_turno) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filtro-group">
                <label>Materia</label>
                <select name="id_materia">
                    <option value="">Todas las materias</option>
                    @foreach($materias as $m)
                    <option value="{{ $m->id }}" {{ $idMateria == $m->id ? 'selected' : '' }}>
                        {{ $m->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filtro-group">
                <label>Carrera</label>
                <select name="carrera">
                    <option value="Todas">Todas las carreras</option>
                    @foreach($carreras as $c)
                    <option value="{{ $c->codigo }}|{{ $c->plan }}|{{ $c->modalidad }}"
                        {{ $carrera == $c->codigo.'|'.$c->plan.'|'.$c->modalidad ? 'selected' : '' }}>
                        {{ $c->nombre }} {{ $c->modalidad === 'virtual' ? '(Virtual)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-filtrar">Consultar</button>
        </form>
    </div>

    {{-- TABLA --}}
    <div class="table-card">
        <div class="table-header">
            <h2>Rendimiento por Postulante — Gestión {{ $gestionCodigo }}</h2>
            <span class="total-badge">{{ $resultado->total() }} postulantes</span>
        </div>

        @if($resultado->isEmpty())
        <div style="text-align:center; padding:40px; color:#888; background:#f8fafc;">
            <div style="font-size:32px; margin-bottom:10px;">📭</div>
            <p style="font-size:14px;">No hay datos disponibles para los filtros seleccionados.</p>
        </div>
        @else
        <div class="table-scroll">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>CI</th>
                        <th>Nombre</th>
                        <th>Grupo</th>
                        @foreach($materiasDisponibles as $m)
                        <th style="text-align:center;">{{ $m->nombre }}</th>
                        @endforeach
                        @if(!$esDocente)
                        <th>Estado</th>
                        @endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultado as $r)
                    <tr>
                        <td><strong>{{ $r['codigo'] }}</strong></td>
                        <td>{{ $r['ci'] }}</td>
                        <td>{{ $r['nombre'] }}</td>
                        <td>{{ $r['id_grupo'] }}</td>
                        @foreach($materiasDisponibles as $m)
                        @php $n = $r['notasPorMateria'][$m->id] ?? null; @endphp
                        <td class="nota-cell">
                            @if($n && $n['notaFinal'] !== null)
                                <div class="nota-val">{{ $n['notaFinal'] }}</div>
                                <div class="nota-estado">
                                    @if($n['aprobado'])
                                        <span style="color:#1a7a3c;">✓</span>
                                    @else
                                        <span style="color:#c0392b;">✗</span>
                                    @endif
                                </div>
                            @else
                                <span style="color:#aaa;">—</span>
                            @endif
                        </td>
                        @endforeach
                        @if(!$esDocente)
                        <td>
                            @if($r['estado'] == 'aprobado') <span class="badge badge-green">Aprobado</span>
                            @elseif($r['estado'] == 'reprobado') <span class="badge badge-red">Reprobado</span>
                            @elseif($r['estado'] == 'inscrito') <span class="badge badge-blue">Inscrito</span>
                            @elseif($r['estado'] == 'preinscrito') <span class="badge badge-yellow">Preinscrito</span>
                            @elseif($r['estado'] == 'baja') <span class="badge badge-dark">Baja</span>
                            @else <span class="badge badge-gray">{{ $r['estado'] }}</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <a href="{{ route('rendimiento.detalle', $r['codigo']) }}"
                                style="padding:5px 10px; background:#dceeff; color:#1a5fa8; border-radius:4px; font-size:11px; font-weight:600; text-decoration:none;">
                                Ver notas
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination-box">
                {{ $resultado->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
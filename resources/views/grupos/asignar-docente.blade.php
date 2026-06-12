@extends('layouts.app')

@section('title', 'Asignar Docente - CUP')
@section('page_title', 'Grupos y Asignación')

@section('content')
<style>
    .wrapper { max-width: 860px; font-family: 'Source Sans 3', sans-serif; }

    .btn-volver {
        display: inline-block;
        padding: 9px 20px;
        background: #cbd5e1;
        color: #1e293b;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 20px;
    }

    .btn-volver:hover { background: #b0bec5; }

    .card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }

    .card-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }

    .card-header h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px; margin: 0; }

    .card-header .meta { font-size: 12px; color: #8aa0b8; margin-top: 4px; }

    .card-body { padding: 24px; }

    .docentes-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        border-radius: 8px;
        overflow: hidden;
    }

    .docentes-table thead { background: #0d3b6e; color: white; }
    .docentes-table th { padding: 10px 14px; text-align: left; font-size: 12px; }
    .docentes-table th:first-child { border-radius: 8px 0 0 0; }
    .docentes-table th:last-child  { border-radius: 0 8px 0 0; }
    .docentes-table td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #333; vertical-align: middle; }
    .docentes-table tr:last-child td { border-bottom: none; }
    .docentes-table tr.disponible:hover td { background: #f0f7ff; }
    .docentes-table tr.no-disponible td { opacity: 0.5; }

    .badge-carga { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .carga-ok    { background: #d4f5e2; color: #1a7a3c; }
    .carga-llena { background: #fde8e8; color: #c0392b; }
    .carga-cruce { background: #fff3cd; color: #856404; }

    .btn-seleccionar {
        padding: 5px 14px;
        background: #0d3b6e;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-seleccionar:hover { background: #0a2d56; }

    .alert-error { background: #fde8e8; color: #c0392b; padding: 12px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }

    .area-badge {
        display: inline-block;
        padding: 2px 8px;
        background: #dceeff;
        color: #1a5fa8;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
        text-transform: capitalize;
        margin: 1px;
    }

    .empty-state { text-align: center; padding: 40px; color: #8aa0b8; font-size: 14px; }
</style>

<div class="wrapper">

    <a href="{{ route('grupos.index', ['gestion' => $codigoGestion]) }}" class="btn-volver">← Volver a grupos</a>

    @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Asignar docente — Grupo {{ $grupoId }}, {{ $grupoMateria->materia->nombre }}</h2>
            <div class="meta">
                Gestión {{ $codigoGestion }} &nbsp;·&nbsp;
                Turno {{ ucfirst($turnoGrupo) }} &nbsp;·&nbsp;
                {{ substr($grupoMateria->hora_inicio, 0, 5) }}–{{ substr($grupoMateria->hora_fin, 0, 5) }} &nbsp;·&nbsp;
                Área requerida: <span class="area-badge">{{ $areaNecesaria }}</span>
            </div>
        </div>
        <div class="card-body">
            @if($docentes->isEmpty())
            <div class="empty-state">
                No hay docentes con área <strong>{{ $areaNecesaria }}</strong> registrados en el sistema.
            </div>
            @else
            <table class="docentes-table">
                <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Nombre</th>
                        <th>Área(s)</th>
                        <th>Grupos asignados</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($docentes->sortByDesc('disponible') as $d)
                    <tr class="{{ $d->disponible ? 'disponible' : 'no-disponible' }}">
                        <td>{{ $d->registro }}</td>
                        <td><strong>{{ $d->datosPersonales->nombre ?? '' }} {{ $d->datosPersonales->apellido ?? '' }}</strong></td>
                        <td>
                            @foreach($d->requisitosPersonal as $req)
                            <span class="area-badge">{{ $req->area }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge-carga {{ $d->total_asignados >= 4 ? 'carga-llena' : 'carga-ok' }}">
                                {{ $d->total_asignados }} / 4
                            </span>
                        </td>
                        <td>
                            @if($d->total_asignados >= 4)
                                <span class="badge-carga carga-llena">Límite alcanzado</span>
                            @elseif($d->cruce_materia)
                                <span class="badge-carga carga-cruce">Cruce de horario</span>
                            @else
                                <span class="badge-carga carga-ok">Disponible</span>
                            @endif
                        </td>
                        <td>
                            @if($d->disponible)
                            <form action="{{ route('grupos.asignarDocente') }}" method="POST">
                                @csrf
                                <input type="hidden" name="grupo"             value="{{ $grupoId }}">
                                <input type="hidden" name="gestion"           value="{{ $codigoGestion }}">
                                <input type="hidden" name="materia"           value="{{ $idMateria }}">
                                <input type="hidden" name="registro_personal" value="{{ $d->registro }}">
                                <button type="submit" class="btn-seleccionar"
                                    onclick="return confirm('¿Asignar a {{ $d->datosPersonales->nombre }} {{ $d->datosPersonales->apellido }}?')">
                                    Asignar
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
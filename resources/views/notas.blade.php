@extends('layouts.app')

@section('title', 'Registro de Notas')

@section('content')
<div class="content" style="padding: 24px; flex: 1;">

    @if(session('success'))
    <div
        style="background: #d4f5e2; border: 1px solid #1a7a3c; color: #1a7a3c; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
        ✓ {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div
        style="background: #fde8e8; border: 1px solid #c0392b; color: #c0392b; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; list-style: none;">
        @foreach($errors->all() as $error)
        <li>• {{ $error }}</li>
        @endforeach
    </div>
    @endif

    <div class="filters-card"
        style="background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 20px 24px; margin-bottom: 20px;">
        <h3
            style="font-size: 13px; font-weight: 600; color: #0d3b6e; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
            Seleccionar Grupo y Materia</h3>

        <form action="{{ url('/docente/grupos-materias') }}" method="GET" class="filters-grid"
            style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 14px; align-items: end;">
            <div>
                <label
                    style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; margin-bottom: 6px;">Grupo</label>
                <select name="id_grupo" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Seleccionar Grupo --</option>
                    @foreach($grupos as $g)
                    <option value="{{ $g->id }}" {{ request('id_grupo')==$g->id ? 'selected' : '' }}>
                        {{ $g->nombre }} · {{ $g->turno }} (Aula {{ $g->aula }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label
                    style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; margin-bottom: 6px;">Materia</label>
                <select name="id_materia" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Seleccionar Materia --</option>
                    @foreach($materias as $m)
                    <option value="{{ $m->id }}" {{ request('id_materia')==$m->id ? 'selected' : '' }}>
                        {{ $m->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label
                    style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; margin-bottom: 6px;">Examen</label>
                <select name="id_examen" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Seleccionar Tipo --</option>
                    @foreach($examenes as $e)
                    <option value="{{ $e->id }}" {{ request('id_examen')==$e->id ? 'selected' : '' }}>
                        {{ $e->nombre_evaluacion }} ({{ $e->porcentaje }}%)
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-buscar"
                style="padding: 10px 20px; background: #1a5fa8; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;">Cargar</button>
        </form>
    </div>

    @if(isset($postulantes) && count($postulantes) > 0)
    <div class="table-card"
        style="background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
        <div class="table-header"
            style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px;">Resultados de la Evaluación
                Seleccionada</h2>
            <span class="info-badge"
                style="background: #dceeff; color: #1a5fa8; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">{{
                count($postulantes) }} estudiantes</span>
        </div>

        <form action="{{ url('/docente/registrar-notes') }}" method="POST">
            @csrf
            <input type="hidden" name="id_grupo" value="{{ request('id_grupo') }}">
            <input type="hidden" name="id_materia" value="{{ request('id_materia') }}">
            <input type="hidden" name="id_examen" value="{{ request('id_examen') }}">

            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead style="background: #0d3b6e; color: white;">
                    <tr>
                        <th style="padding: 12px 16px; text-align: left;">#</th>
                        <th style="padding: 12px 16px; text-align: left;">Código Postulante</th>
                        <th style="padding: 12px 16px; text-align: left;">CI</th>
                        <th style="padding: 12px 16px; text-align: left;">Nombre Completo</th>
                        <th style="padding: 12px 16px; text-align: center; width: 130px;">Nota (0-100)</th>
                        <th style="padding: 12px 16px; text-align: left;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($postulantes as $index => $postulante)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px 16px;">{{ $index + 1 }}</td>
                        <td style="padding: 10px 16px;"><strong>{{ $postulante->codigo }}</strong></td>
                        <td style="padding: 10px 16px;">{{ $postulante->ci }}</td>
                        <td style="padding: 10px 16px;">{{ $postulante->apellido }}, {{ $postulante->nombre }}</td>
                        <td style="padding: 10px 16px; text-align: center;">
                            <input type="number" name="notas[{{ $postulante->codigo }}]" class="nota-input" min="0"
                                max="100" value="{{ $postulante->nota_actual ?? '' }}"
                                style="width: 70px; padding: 6px 10px; border: 1.5px solid #e2e8f0; border-radius: 4px; text-align: center;"
                                placeholder="--">
                        </td>
                        <td style="padding: 10px 16px;">
                            @if(is_null($postulante->nota_actual))
                            <span class="badge"
                                style="background: #e2e8f0; color: #5a5a5a; padding: 3px 10px; border-radius: 12px; font-weight:600;">Pendiente</span>
                            @elseif($postulante->nota_actual >= 51)
                            <span class="badge"
                                style="background: #d4f5e2; color: #1a7a3c; padding: 3px 10px; border-radius: 12px; font-weight:600;">Aprobado</span>
                            @else
                            <span class="badge"
                                style="background: #fde8e8; color: #c0392b; padding: 3px 10px; border-radius: 12px; font-weight:600;">Reprobado</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="footer-buttons"
                style="display: flex; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                <button type="submit" class="btn-save"
                    style="padding: 11px 28px; border: none; border-radius: 6px; background: #0d3b6e; color: white; font-weight: 600; cursor: pointer;">💾
                    Guardar Notas</button>
            </div>
        </form>
    </div>
    @elseif(request('id_grupo'))
    <div
        style="background: white; border-radius: 10px; padding: 24px; text-align: center; color: #5a5a5a; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        No se encontraron postulantes asignados a la combinación de grupo y materia seleccionada.
    </div>
    @endif
</div>
@endsection
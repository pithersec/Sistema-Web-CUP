@extends('layouts.app')

@section('title', 'Registro de Notas - CUP')
@section('page_title', 'Registro de Notas')

@section('content')
<div class="content" style="padding: 0; width: 100%;">

    @if(session('success'))
    <div
        style="padding: 12px; background: #d4f5e2; color: #1a7a3c; border-radius: 6px; margin-bottom: 16px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
    @endif

    <div class="filters-card"
        style="background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 20px 24px; margin-bottom: 20px;">
        <h3
            style="font-size: 13px; font-weight: 600; color: #0d3b6e; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
            Seleccionar Grupo y Materia</h3>

        {{-- <form action="{{ url('/docente/grupos-notas') }}" method="GET" class="filters-grid" --}} <form
            action="{{ url('/docente/registrar-notas') }}" method="GET" class="filters-grid"
            style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 14px; align-items: end;">
            <div>
                <label
                    style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px;">Grupo</label>
                <select name="id_grupo" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="">-- Seleccionar Grupo --</option>
                    @foreach($grupos as $g)
                    <option value="{{ $g->id }}" {{ request('id_grupo')==$g->id ? 'selected' : '' }}>
                        {{ $g->nombre }} · {{ $g->turno }} · Aula {{ $g->aula }}
                    </option>
                    @endforeach
                </select>

                {{-- <select name="id_grupo" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="">-- Seleccionar Grupo --</option>
                    {{-- Aquí iterarás tus grupos reales --}}
                    {{-- @foreach($grupos as $g) --}}
                    {{--}} <option value="1" {{ request('id_grupo')==1 ? 'selected' : '' }}>Grupo A · Mañana ·
                        Aula 101
                    </option>
                    <option value="2" {{ request('id_grupo')==2 ? 'selected' : '' }}>Grupo B · Tarde · Aula 202
                    </option>
                    --}} {{-- @endforeach --}}
                    {{--
                </select> --}}
            </div>
            <div>
                <label
                    style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px;">Materia</label>

                <select name="id_materia" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="">-- Seleccionar Materia --</option>
                    @foreach($materias as $m)
                    <option value="{{ $m->id }}" {{ request('id_materia')==$m->id ? 'selected' : '' }}>
                        {{ $m->nombre }}
                    </option>
                    @endforeach
                </select>
                {{--<select name="id_materia" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="">-- Seleccionar Materia --</option>
                    <option value="1" {{ request('id_materia')==1 ? 'selected' : '' }}>Matemáticas</option>
                    <option value="2" {{ request('id_materia')==2 ? 'selected' : '' }}>Computación</option>
                    <option value="3" {{ request('id_materia')==3 ? 'selected' : '' }}>Inglés</option>
                    <option value="4" {{ request('id_materia')==4 ? 'selected' : '' }}>Física</option>
                </select> --}}
            </div>
            <div>
                <label
                    style="display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px;">Examen</label>
                <select name="nro_examen" required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: white; outline: none;">
                    <option value="1" {{ request('nro_examen')==1 ? 'selected' : '' }}>Examen 1 (30%)</option>
                    <option value="2" {{ request('nro_examen')==2 ? 'selected' : '' }}>Examen 2 (30%)</option>
                    <option value="3" {{ request('nro_examen')==3 ? 'selected' : '' }}>Examen 3 (40%)</option>
                </select>
            </div>
            <button type="submit" class="btn-buscar"
                style="padding: 10px 20px; background: #1a5fa8; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap;">Cargar
                Estudiantes</button>
        </form>
    </div>

    <div class="table-card"
        style="background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
        <div class="table-header"
            style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px;">Planilla de Evaluación
                Actual</h2>
            <span class="info-badge"
                style="background: #dceeff; color: #1a5fa8; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">6
                estudiantes en lista</span>
        </div>

        <form action="{{ url('/docente/registrar-notas') }}" method="POST">
            @csrf
            {{-- Campos ocultos para saber a qué grupo/materia aplicarle las notas al procesar el envío --}}
            <input type="hidden" name="id_grupo" value="{{ request('id_grupo') }}">
            <input type="hidden" name="id_materia" value="{{ request('id_materia') }}">
            <input type="hidden" name="nro_examen" value="{{ request('nro_examen') }}">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead style="background: #0d3b6e; color: white;">
                    <tr>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px;">#</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px;">CI</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px;">Nombre
                            Completo</th>
                        <th
                            style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; width: 120px;">
                            Nota
                            (0-100)</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @if($postulantes && count($postulantes) > 0)
                    @foreach($postulantes as $index => $pos)
                    <tr>
                        <td style="padding: 10px 16px; border-bottom: 1px solid #e2e8f0;">{{ $index + 1 }}</td>
                        <td style="padding: 10px 16px; border-bottom: 1px solid #e2e8f0;">{{ $pos->ci }}</td>
                        <td style="padding: 10px 16px; border-bottom: 1px solid #e2e8f0;">{{ $pos->nombre }} {{
                            $pos->apellido }}
                        </td>
                        <td style="padding: 10px 16px; border-bottom: 1px solid #e2e8f0;">
                            <input type="number" name="notas[{{ $pos->codigo }}]" class="nota-input" min="0" max="100"
                                value="{{ $pos->nota_actual }}"
                                style="width: 70px; padding: 6px 10px; border: 1.5px solid #e2e8f0; border-radius: 4px; text-align: center;" />
                        </td>
                        <td style="padding: 10px 16px; border-bottom: 1px solid #e2e8f0;">
                            @if($pos->nota_actual === null)
                            <span class="badge badge-gray"
                                style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; background: #e2e8f0; color: #5a5a5a;">Pendiente</span>
                            @elseif($pos->nota_actual >= 51)
                            <span class="badge badge-green"
                                style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; background: #d4f5e2; color: #1a7a3c;">Aprobado</span>
                            @else
                            <span class="badge badge-red"
                                style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; background: #fde8e8; color: #c0392b;">Reprobado</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #5a5a5a; background: #f8fafc;">
                            Por favor, seleccione los filtros de arriba y haga clic en "Cargar Estudiantes".
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <div class="footer-buttons"
                style="display: flex; justify-content: flex-end; gap: 12px; padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                <button type="submit" class="btn-save"
                    style="padding: 11px 28px; border: none; border-radius: 6px; background: #0d3b6e; color: white; font-size: 14px; font-weight: 600; cursor: pointer;">💾
                    Guardar Notas de la Planilla</button>
            </div>
        </form>
    </div>
</div>
@endsection
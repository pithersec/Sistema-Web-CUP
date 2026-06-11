@extends('layouts.app')

@section('title', 'Resultado de Carga - CUP')
@section('page_title', 'Cargar Cuentas Masivas')

@section('content')
<style>
    .resultado-wrapper {
        max-width: 780px;
        font-family: 'Source Sans 3', sans-serif;
        margin: auto;
    }

    .btn-volver {
        display: inline-block;
        padding: 9px 20px;
        background: #f1f5f9;
        color: #333;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 20px;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-volver:hover { background: #e2e8f0; }

    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-header h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 15px;
        margin: 0;
    }

    .card-body { padding: 24px; }

    .resumen-counters {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .counter-badge {
        padding: 6px 18px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }

    .counter-creados  { background: #d4f5e2; color: #1a7a3c; }
    .counter-omitidos { background: #fff3cd; color: #856404; }
    .counter-errores  { background: #fde8e8; color: #c0392b; }

    .seccion-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 8px;
        margin-top: 20px;
    }

    .resumen-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .resumen-table thead tr th:first-child { border-radius: 8px 0 0 0; }
    .resumen-table thead tr th:last-child  { border-radius: 0 8px 0 0; }

    .resumen-table thead { background: #0d3b6e; color: white; }
    .resumen-table th { padding: 10px 14px; text-align: left; font-size: 12px; }
    .resumen-table td { padding: 9px 14px; border-bottom: 1px solid #e2e8f0; color: #333; }
    .resumen-table tr:last-child td { border-bottom: none; }
    .resumen-table tr:hover td { background: #f8fafc; }

    .problemas-list {
        margin: 0;
        padding-left: 18px;
        font-size: 13px;
        line-height: 2;
    }
</style>

<div class="resultado-wrapper">

    <a href="{{ route('usuarios.cargar') }}" class="btn-volver">← Nueva carga</a>

    <div class="card">
        <div class="card-header">
            <h2>Resultado de la carga masiva</h2>
        </div>
        <div class="card-body">

            <div class="resumen-counters">
                <span class="counter-badge counter-creados">✔ {{ count($creados) }} creados</span>
                @if(count($omitidos) > 0)
                <span class="counter-badge counter-omitidos">⚠ {{ count($omitidos) }} omitidos</span>
                @endif
                @if(count($errores) > 0)
                <span class="counter-badge counter-errores">✖ {{ count($errores) }} con error</span>
                @endif
            </div>

            @if(count($creados) > 0)
            <div class="seccion-label" style="color: #1a7a3c; margin-top: 0;">Cuentas creadas</div>
            <table class="resumen-table">
                <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Perfil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($creados as $c)
                    <tr>
                        <td>{{ $c['registro'] }}</td>
                        <td>{{ $c['nombre'] }}</td>
                        <td><code style="background:#f1f5f9; padding:2px 8px; border-radius:4px; font-size:12px;">{{ $c['user_name'] }}</code></td>
                        <td>{{ $c['perfil'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if(count($omitidos) > 0)
            <div class="seccion-label" style="color: #856404;">Omitidos (duplicados)</div>
            <ul class="problemas-list">
                @foreach($omitidos as $o)
                <li style="color:#856404;">{{ $o }}</li>
                @endforeach
            </ul>
            @endif

            @if(count($errores) > 0)
            <div class="seccion-label" style="color: #c0392b;">Con errores</div>
            <ul class="problemas-list">
                @foreach($errores as $e)
                <li style="color:#c0392b;">{{ $e }}</li>
                @endforeach
            </ul>
            @endif

        </div>
    </div>

    <div style="display:flex; gap:10px;">
        <a href="{{ route('usuarios.cargar') }}"
            style="padding:10px 20px; background:#0d3b6e; color:white; border-radius:6px; font-size:13px; font-weight:600; text-decoration:none;">
            Cargar otro archivo
        </a>
        <a href="{{ route('personal.index') }}"
            style="padding:10px 20px; background:#cbd5e1; color:#1e293b; border-radius:6px; font-size:13px; font-weight:600; text-decoration:none;">
            Ir a Personal
        </a>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('title', 'Editar Usuario - CUP')
@section('page_title', 'Editar Usuario')

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 28px;
        max-width: 600px;
    }
    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #0d3b6e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .form-group input, .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        font-family: 'Source Sans 3', sans-serif;
    }
    .form-group input:focus, .form-group select:focus { border-color: #2980b9; }
    .form-group .hint { font-size: 11px; color: #888; margin-top: 4px; }
    .btn-primary {
        padding: 11px 28px; border: none; border-radius: 6px;
        background: #0d3b6e; color: white; font-size: 14px; font-weight: 600;
        cursor: pointer; font-family: 'Source Sans 3', sans-serif;
    }
    .btn-primary:hover { background: #1a5fa8; }
    .btn-cancel {
        padding: 11px 28px; border: 1.5px solid #e2e8f0; border-radius: 6px;
        background: white; color: #5a5a5a; font-size: 14px; font-weight: 600;
        cursor: pointer; font-family: 'Source Sans 3', sans-serif; text-decoration: none;
    }
    .alert-error {
        background: #fde8e8; border: 1px solid #f8b4b4; color: #9b1c1c;
        padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 16px;
    }
</style>

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $error)
    {{ $error }}<br>
    @endforeach
</div>
@endif

<div class="form-card">
    <h2 style="font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 18px; margin-bottom: 20px;">Editar Usuario: {{ $usuario->user_name }}</h2>

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nombre de Usuario</label>
            <input type="text" name="user_name" value="{{ old('user_name', $usuario->user_name) }}" required />
        </div>

        <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" />
        </div>

        <div class="form-group">
            <label>Nueva Contraseña</label>
            <input type="password" name="clave" placeholder="Dejar vacío para no cambiar" />
            <div class="hint">Mínimo 6 caracteres. Dejar vacío si no desea cambiar la contraseña.</div>
        </div>

        <div class="form-group">
            <label>Perfil Asignado</label>
            <select name="id_perfil" required>
                @foreach($perfiles as $perf)
                <option value="{{ $perf->id }}" {{ $usuario->id_perfil == $perf->id ? 'selected' : '' }}>{{ $perf->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn-primary">Guardar Cambios</button>
            <a href="{{ route('usuarios.index') }}" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
@endsection
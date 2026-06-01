@extends('layouts.app')

@define('title', 'Cerrar Sesión - CUP')
@section('page_title', 'Cerrar Sesión')

@section('content')
<div class="modal">
    <div class="icon-circle">🚪</div>
    <h2>¿Cerrar sesión?</h2>
    <p>Estás a punto de cerrar la sesión de <strong>{{ Auth::user()->user_name ?? 'Usuario' }}</strong>. Deberás volver
        a iniciar sesión para acceder al sistema.</p>

    <form action="{{ url('/logout') }}" method="POST">
        @csrf
        <div class="buttons">
            <a href="{{ url('/dashboard') }}" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn-confirm">Cerrar Sesión</button>
        </div>
    </form>

    <div class="session-info">
        <p>Sesión activa · Rol: <strong>{{ Auth::user()->perfil->nombre ?? 'Administrador' }}</strong></p>
    </div>
</div>
@endsection
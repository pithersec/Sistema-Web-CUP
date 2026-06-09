@extends('layouts.app')

@section('title', 'Acceso Denegado - CUP')
@section('page_title', 'Acceso Denegado')

@section('content')
<div style="width:100%; display:flex; align-items:center; justify-content:center; min-height:60vh;">
    <div style="text-align:center; padding:60px 20px; max-width:500px;">
        <div style="font-size:64px; margin-bottom:16px;">🔒</div>
        <h2 style="color:#0d3b6e; margin-bottom:12px;">No tienes permiso para acceder a esta sección</h2>
        <p style="color:#5a5a5a; font-size:15px; margin-bottom:24px;">
            Tu perfil no cuenta con los privilegios necesarios. Contacta al administrador del sistema si crees que es un error.
        </p>
        <a href="{{ route('dashboard') }}" style="display:inline-block; padding:12px 28px; background:#0d3b6e; color:white; border-radius:8px; text-decoration:none; font-weight:600;">
            Volver al Dashboard
        </a>
    </div>
</div>
@endsection
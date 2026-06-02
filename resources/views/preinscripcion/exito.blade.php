@extends('layouts.app')

@section('title', 'Preinscripción Exitosa - CUP')
@section('page_title', 'Preinscripción Exitosa')

@section('content')
<div style="text-align:center; padding:60px 20px;">
    <div style="font-size:64px; margin-bottom:16px;">&#10003;</div>
    <h2 style="color:#0d3b6e; margin-bottom:12px;">¡Preinscripción registrada con éxito!</h2>
    <p style="color:#5a5a5a; font-size:15px; margin-bottom:12px;">
        Tu código de postulante es: <strong>{{ session('codigo_postulante') }}</strong>
    </p>
    <p style="color:#5a5a5a; font-size:15px; margin-bottom:24px;">
        Fecha límite de validación: <strong>{{ session('plazo_limite') }}</strong>
    </p>
    <a href="{{ route('login') }}" style="display:inline-block; padding:12px 28px; background:#0d3b6e; color:white; border-radius:8px; text-decoration:none; font-weight:600;">
        Ir al Inicio
    </a>
</div>
@endsection
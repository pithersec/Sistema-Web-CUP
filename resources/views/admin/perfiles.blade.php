@extends('layouts.app')

@section('title', 'Gestión de Perfiles y Privilegios - CUP')
@section('page_title', 'Gestión de Perfiles y Privilegios')

@section('content')
<style>
    .perfiles-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }
    .perfil-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 24px; margin-bottom: 20px; }
    .perfil-card h3 { color: #0d3b6e; font-size: 18px; margin-bottom: 6px; }
    .perfil-card p { color: #5a5a5a; font-size: 13px; margin-bottom: 16px; }
    .priv-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    .priv-badge { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    .priv-badge.active { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .priv-badge.inactive { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }
</style>

<div class="perfiles-wrapper">
    @if(session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:8px;margin-bottom:20px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @foreach($perfiles as $perfil)
    <div class="perfil-card">
        <h3>{{ $perfil->nombre }}</h3>
        <p>{{ $perfil->descripcion }}</p>
        <div class="priv-grid">
            @foreach($privilegios as $priv)
                @php
                    $activo = $perfil->privilegios->contains('id', $priv->id);
                @endphp
                <span class="priv-badge {{ $activo ? 'active' : 'inactive' }}">
                    {{ $activo ? '✓' : '—' }} {{ $priv->nombre }}
                </span>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
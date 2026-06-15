@extends('layouts.app')

@section('title', 'Panel de Indicadores - CUP')
@section('page_title', 'Panel de Indicadores')

@section('content')
<style>
    .dashboard-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }
    .dash-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .dash-header h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px; font-weight: 700; }
    .gestion-select-wrap { display: flex; align-items: center; gap: 10px; }
    .gestion-select-wrap label { font-size: 13px; font-weight: 600; color: #5a5a5a; white-space: nowrap; }
    .gestion-select { padding: 8px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Source Sans 3', sans-serif; font-size: 13px; color: #0d3b6e; font-weight: 600; background: white; cursor: pointer; outline: none; }
    .gestion-select:focus { border-color: #1a5fa8; }

    .cards-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-bottom: 28px; }
    .kpi-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-top: 4px solid #1a5fa8; transition: transform 0.2s; }
    .kpi-card:hover { transform: translateY(-2px); }
    .kpi-card:nth-child(1) { border-top-color: #1a5fa8; }
    .kpi-card:nth-child(1) .kpi-value { color: #0d3b6e; }
    .kpi-card:nth-child(2) { border-top-color: #2c038b; }
    .kpi-card:nth-child(2) .kpi-value { color: #2c038b; }
    .kpi-card:nth-child(3) { border-top-color: #2793ae; }
    .kpi-card:nth-child(3) .kpi-value { color: #27ae60; }
    .kpi-card:nth-child(4) { border-top-color: #c0392b; }
    .kpi-card:nth-child(4) .kpi-value { color: #c0392b; }
    .kpi-card:nth-child(5) { border-top-color: #0891b2; }
    .kpi-card:nth-child(5) .kpi-value { color: #0891b2; }
    .kpi-card:nth-child(6) { border-top-color: #f39c12; }
    .kpi-card:nth-child(6) .kpi-value { color: #f39c12; }
    .kpi-icon { font-size: 24px; margin-bottom: 8px; }
    .kpi-value { font-family: 'Merriweather', serif; font-size: 32px; font-weight: 700; color: #0d3b6e; }
    .kpi-label { font-size: 11px; color: #5a5a5a; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 600; margin-top: 4px; }

    .section-title { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 15px; margin-bottom: 14px; font-weight: 700; }
    .paquetes-fila { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 8px; align-items: start; }
    .paquete-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .paquete-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; cursor: pointer; user-select: none; transition: background 0.15s; }
    .paquete-header:hover { background: #f8fafc; }
    .paquete-title { font-size: 13px; font-weight: 700; color: #0d3b6e; }
    .paquete-sub { font-size: 11px; color: #888; margin-top: 2px; }
    .paquete-chevron { font-size: 12px; color: #aaa; transition: transform 0.2s; display: inline-block; }
    .paquete-body { padding: 0 18px; display: none;}
    .cu-item:last-child { border-bottom: none; }
    .cu-id { font-size: 10px; font-weight: 700; color: white; padding: 2px 7px; border-radius: 10px; white-space: nowrap; }

    .cu-item { 
        display: flex; 
        align-items: flex-start; 
        gap: 10px; 
        padding: 7px 0; 
        border-bottom: 1px solid #f0f4f8; 
        font-size: 12.5px; 
        color: #333; 
        flex-wrap: wrap;
        cursor: pointer;
    }
    .cu-text { flex: 1; }
    .cu-chevron { font-size: 14px; color: #aaa; transition: transform 0.2s; margin-left: auto; }
    .cu-privs { 
        display: none; 
        width: 100%; 
        padding: 6px 0 2px 0; 
        display: none;
        flex-wrap: wrap; 
        gap: 5px; 
    }
    .priv-badge { 
        font-size: 10px; 
        background: #f0f4f8; 
        color: #5a5a5a; 
        padding: 2px 8px; 
        border-radius: 10px; 
        font-family: monospace;
        border: 1px solid #e2e8f0;
    }

    @media (max-width: 768px) {
        .cards-grid { grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .kpi-card { padding: 16px; }
        .kpi-value { font-size: 26px; }
        .kpi-icon { font-size: 20px; }
        .dash-header { flex-direction: column; align-items: flex-start; }
        .paquetes-fila { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
        .paquetes-fila { grid-template-columns: 1fr; }
    }
</style>

<div class="dashboard-wrapper">

    <div class="dash-header">
        <h2>Resumen General</h2>
        <form method="GET" action="{{ route('dashboard') }}" class="gestion-select-wrap">
            <label>Gestión:</label>
            <select name="gestion" class="gestion-select" onchange="this.form.submit()">
                @foreach($gestiones as $g)
                    <option value="{{ $g->codigo }}" {{ $g->codigo == $gestionCodigo ? 'selected' : '' }}>
                        {{ $g->codigo }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="cards-grid">
        <div class="kpi-card">
            <div class="kpi-icon">🎯</div>
            <div class="kpi-value">{{ $kpis['cupos_totales'] }}</div>
            <div class="kpi-label">Cupos Totales</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">👥</div>
            <div class="kpi-value">{{ $kpis['total_inscritos'] }}</div>
            <div class="kpi-label">Total Inscritos</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">✅</div>
            <div class="kpi-value">{{ $kpis['total_aprobados'] }}</div>
            <div class="kpi-label">Total Aprobados</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">❌</div>
            <div class="kpi-value">{{ $kpis['total_reprobados'] }}</div>
            <div class="kpi-label">Total Reprobados</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">📈</div>
            <div class="kpi-value">{{ $kpis['tasa_aprobacion'] }}%</div>
            <div class="kpi-label">Tasa de Aprobación</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">🏫</div>
            <div class="kpi-value">{{ $kpis['grupos_habilitados'] }}</div>
            <div class="kpi-label">Grupos Habilitados</div>
        </div>
    </div>

    <h2 class="section-title">Paquetes del Sistema</h2>
    <div class="paquetes-fila">

        <div class="paquete-card">
            <div class="paquete-header" style="border-left: 4px solid #1a5fa8;" onclick="togglePaquete(this)">
                <div>
                    <div class="paquete-title">P1 · Gestión de Admisión</div>
                    <div class="paquete-sub">4 casos de uso</div>
                </div>
                <span class="paquete-chevron">▼</span>
            </div>
            <div class="paquete-body">
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#1a5fa8;">CU-03</span>
                    <span class="cu-text">Realizar preinscripción</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Postulantes</span><span class="priv-badge">Editar Postulantes</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#1a5fa8;">CU-04</span>
                    <span class="cu-text">Realizar pago</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Postulantes</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#1a5fa8;">CU-05</span>
                    <span class="cu-text">Consultar estado de admisión</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Postulantes</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#1a5fa8;">CU-06</span>
                    <span class="cu-text">Presentar reclamo</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Gestionar Reclamos</span></div>
                </div>
            </div>
        </div>

        <div class="paquete-card">
            <div class="paquete-header" style="border-left: 4px solid #2c038b;" onclick="togglePaquete(this)">
                <div>
                    <div class="paquete-title">P2 · Gestión Académica</div>
                    <div class="paquete-sub">5 casos de uso</div>
                </div>
                <span class="paquete-chevron">▼</span>
            </div>
            <div class="paquete-body">
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#2c038b;">CU-07</span>
                    <span class="cu-text">Registrar notas</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Exámenes</span><span class="priv-badge">Registrar Notas</span><span class="priv-badge">Editar Notas</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#2c038b;">CU-08</span>
                    <span class="cu-text">Consultar rendimiento académico</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Rendimiento Académico</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#2c038b;">CU-11</span>
                    <span class="cu-text">Generar asignación de grupos</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Grupos</span><span class="priv-badge">Asignar Grupos</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#2c038b;">CU-12</span>
                    <span class="cu-text">Atender reclamos</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Gestionar Reclamos</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#2c038b;">CU-20</span>
                    <span class="cu-text">Gestionar asistencia</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Registrar Asistencia</span></div>
                </div>
            </div>
        </div>

        <div class="paquete-card">
            <div class="paquete-header" style="border-left: 4px solid #0891b2;" onclick="togglePaquete(this)">
                <div>
                    <div class="paquete-title">P3 · Gestión Administrativa</div>
                    <div class="paquete-sub">6 casos de uso</div>
                </div>
                <span class="paquete-chevron">▼</span>
            </div>
            <div class="paquete-body">
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#0891b2;">CU-13</span>
                    <span class="cu-text">Gestionar postulantes</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Postulantes</span><span class="priv-badge">Editar Postulantes</span><span class="priv-badge">Dar de Baja Postulantes</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#0891b2;">CU-14</span>
                    <span class="cu-text">Gestionar personal</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Personal</span><span class="priv-badge">Registrar Personal</span><span class="priv-badge">Editar Personal</span><span class="priv-badge">Desactivar Personal</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#0891b2;">CU-15</span>
                    <span class="cu-text">Gestionar carreras y cupos</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Carreras</span><span class="priv-badge">Editar Cupos</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#0891b2;">CU-16</span>
                    <span class="cu-text">Gestionar usuarios y perfiles</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Usuarios</span><span class="priv-badge">Editar Usuarios</span><span class="priv-badge">Gestionar Perfiles</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#0891b2;">CU-17</span>
                    <span class="cu-text">Cargar cuentas masivas</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Cargar Cuentas Masivas</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#0891b2;">CU-18</span>
                    <span class="cu-text">Configurar parámetros</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Configurar Parámetros</span><span class="priv-badge">Ver Gestiones</span><span class="priv-badge">Gestionar Gestiones</span><span class="priv-badge">Gestionar Materias</span></div>
                </div>
            </div>
        </div>

        <div class="paquete-card">
            <div class="paquete-header" style="border-left: 4px solid #f39c12;" onclick="togglePaquete(this)">
                <div>
                    <div class="paquete-title">P4 · Seguridad y Reportes</div>
                    <div class="paquete-sub">5 casos de uso</div>
                </div>
                <span class="paquete-chevron">▼</span>
            </div>
            <div class="paquete-body">
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#f39c12;">CU-01</span>
                    <span class="cu-text">Iniciar sesión</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Acceso Total</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#f39c12;">CU-02</span>
                    <span class="cu-text">Cerrar sesión</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Acceso Total</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#f39c12;">CU-09</span>
                    <span class="cu-text">Consultar indicadores estadísticos</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Reportes</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#f39c12;">CU-10</span>
                    <span class="cu-text">Generar reportes</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Reportes</span></div>
                </div>
                <div class="cu-item" onclick="toggleCU(this)">
                    <span class="cu-id" style="background:#f39c12;">CU-19</span>
                    <span class="cu-text">Consultar bitácora</span>
                    <span class="cu-chevron">›</span>
                    <div class="cu-privs"><span class="priv-badge">Ver Bitácora</span></div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function togglePaquete(header) {
        const body = header.nextElementSibling;
        const chevron = header.querySelector('.paquete-chevron');
        const isOpen = body.style.display === 'block';

        if (!isOpen) {
            body.style.display = 'block';
            body.style.padding = '12px 18px';
            body.style.borderTop = '1px solid #e2e8f0';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            body.style.display = 'none';
            body.style.padding = '0 18px';
            body.style.borderTop = '0';
            chevron.style.transform = 'rotate(0deg)';
        }
    }
    function toggleCU(item) {
        const privs = item.querySelector('.cu-privs');
        const chevron = item.querySelector('.cu-chevron');
        const isOpen = privs.style.display === 'flex';
        privs.style.display = isOpen ? 'none' : 'flex';
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
    }
</script>
@endsection
@extends('layouts.app')

@section('title', 'Configuración de Parámetros - CUP')
@section('page_title', 'Configuración de Parámetros')

@section('content')
<style>
    .param-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }

    .section-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 20px 24px;
        margin-bottom: 20px;
    }
    .section-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--azul);
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gestion-badge {
        background: var(--azul);
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }
    .cerrada-badge {
        background: #e2e8f0;
        color: #555;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group label { font-size: 12px; font-weight: 600; color: #555; }
    .form-group input {
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Source Sans 3', sans-serif;
        background: white;
        color: #333;
        transition: border-color .15s;
    }
    .form-group input:focus { outline: none; border-color: var(--azul-claro); }
    .form-group input:disabled { background: #f5f7fa; color: #888; cursor: not-allowed; }
    .field-hint { font-size: 11px; color: #888; margin-top: 2px; }

    /* Secciones 2 y 3 lado a lado */
    .two-col { display: flex; gap: 20px; align-items: stretch; margin-bottom: 20px; }
    .two-col .section-card { flex: 1; margin-bottom: 0; display: flex; flex-direction: column; }

    /* Turno grid */
    .turno-grid {
        display: grid;
        grid-template-columns: 110px 1fr 1fr;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }
    .turno-label { font-size: 13px; font-weight: 600; color: var(--azul); }
    .col-header { font-size: 11px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.4px; }

    /* Materia rows */
    .materia-row {
        display: grid;
        grid-template-columns: 1fr 130px;
        gap: 10px;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .materia-row:last-child { border-bottom: none; }
    .materia-nombre { font-size: 13px; color: #333; }

    /* Preview horario */
    .preview-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 16px;
        margin-top: 16px;
        flex: 1;
    }
    .preview-title { font-size: 11px; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
    .preview-row {
        display: grid;
        grid-template-columns: 90px 160px 1fr;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }
    .preview-row:last-child { border-bottom: none; }
    .preview-turno { font-weight: 600; color: var(--azul); }
    .preview-fin { color: #555; }
    .preview-ok { color: #1a7431; font-weight: 600; }
    .preview-error { color: var(--rojo); font-weight: 600; }

    /* Botones */
    .btn-row { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
    .btn {
        padding: 10px 22px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Source Sans 3', sans-serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background .15s, transform .1s;
        text-decoration: none;
    }
    .btn:active { transform: scale(0.97); }
    .btn-primary { background: var(--azul); color: white; }
    .btn-primary:hover { background: var(--azul-claro); }
    .btn-danger  { background: var(--rojo); color: white; }
    .btn-danger:hover  { background: var(--rojo-claro); }
    .btn-success { background: #1a7431; color: white; }
    .btn-success:hover { background: #24a046; }

    .readonly-notice {
        background: #fff3cd;
        border: 1px solid #f0ad4e;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 13px;
        color: #856404;
        margin-bottom: 16px;
    }
    .warning-box {
        background: #fff7ed;
        border: 1px solid #fdba74;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 13px;
        color: #9a3412;
        margin-bottom: 16px;
    }
    .nueva-gestion-card {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 20px;
    }
    .nueva-gestion-card .section-label { color: #166534; }
</style>

<div class="param-wrapper">

    @if(session('success'))
    <div style="background:#d4f5e2;color:#1a7a3c;padding:12px;border-radius:6px;margin-bottom:16px;font-size:13px;font-weight:600;">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fde8e8;color:#c0392b;padding:12px;border-radius:6px;margin-bottom:16px;font-size:13px;font-weight:600;">
        ❌ {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div style="background:#fde8e8;color:#c0392b;padding:12px;border-radius:6px;margin-bottom:16px;font-size:13px;">
        @foreach($errors->all() as $error)<div>❌ {{ $error }}</div>@endforeach
    </div>
    @endif

    @if($gestion)
    <form method="POST" action="{{ route('parametros.modificar') }}" id="form-parametros">
        @csrf
        @method('PUT')

        {{-- SECCIÓN 1: GESTIÓN --}}
        <div class="section-card">
            <div class="section-label">
                Gestión Activa
                <span class="{{ $gestionCerrada ? 'cerrada-badge' : 'gestion-badge' }}">
                    {{ $gestion->codigo }} — {{ $gestionCerrada ? 'Cerrada' : 'En curso' }}
                </span>
            </div>
            @if($gestionCerrada)
            <div class="readonly-notice">⚠ Esta gestión ya concluyó. Los parámetros son de solo lectura.</div>
            @endif
            <div class="form-grid">
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_ini" value="{{ $gestion->fecha_ini }}" {{ $gestionCerrada ? 'disabled' : '' }}>
                </div>
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $gestion->fecha_fin }}" {{ $gestionCerrada ? 'disabled' : '' }}>
                </div>
                <div class="form-group">
                    <label>Inicio Registro de Notas</label>
                    <input type="date" name="fecha_inicio_notas" value="{{ $gestion->fecha_inicio_notas }}" {{ $gestionCerrada ? 'disabled' : '' }}>
                </div>
                <div class="form-group">
                    <label>Fin Registro de Notas</label>
                    <input type="date" name="fecha_fin_notas" value="{{ $gestion->fecha_fin_notas }}" {{ $gestionCerrada ? 'disabled' : '' }}>
                </div>
                <div class="form-group">
                    <label>Nota Mínima de Aprobación</label>
                    <input type="number" name="nota_minima" value="{{ $gestion->nota_minima ?? 60 }}" min="0" max="100" {{ $gestionCerrada ? 'disabled' : '' }}>
                    <span class="field-hint">Por defecto: 60</span>
                </div>
            </div>
        </div>

        {{-- SECCIONES 2 Y 3 LADO A LADO --}}
        <div class="two-col">

            {{-- SECCIÓN 2: TURNOS --}}
            <div class="section-card">
                <div class="section-label">Horarios de Turnos</div>
                <div class="turno-grid">
                    <span class="col-header">Turno</span>
                    <span class="col-header">Hora Inicio</span>
                    <span class="col-header">Hora Fin</span>
                </div>
                @foreach($turnos as $turno)
                <div class="turno-grid">
                    <span class="turno-label">{{ ucfirst($turno->nombre) }}</span>
                    <input type="time" name="turnos[{{ $turno->nombre }}][hora_inicio]"
                            id="turno_ini_{{ $turno->nombre }}"
                            value="{{ substr($turno->hora_inicio, 0, 5) }}"
                            class="turno-input" {{ $gestionCerrada ? 'disabled' : '' }}>
                    <input type="time" name="turnos[{{ $turno->nombre }}][hora_fin]"
                            id="turno_fin_{{ $turno->nombre }}"
                            value="{{ substr($turno->hora_fin, 0, 5) }}"
                            class="turno-input" {{ $gestionCerrada ? 'disabled' : '' }}>
                </div>
                @endforeach

                {{-- Preview --}}
                <div class="preview-box" id="preview-horario">
                    <div class="preview-title">Horario Proyectado</div>
                    <div id="preview-content"></div>
                </div>
            </div>

            {{-- SECCIÓN 3: MATERIAS --}}
            <div class="section-card">
                <div class="section-label">Duración de Materias <span style="font-weight:400;text-transform:none;font-size:11px;color:#888;">(horas)</span></div>
                @foreach($materias as $materia)
                <div class="materia-row">
                    <span class="materia-nombre">{{ $materia->nombre }}</span>
                    <input type="number" name="materias[{{ $materia->id }}][duracion]"
                            id="materia_dur_{{ $materia->id }}"
                            value="{{ $materia->duracion }}"
                            step="0.5" min="0.5" max="8"
                            class="materia-input"
                            {{ $gestionCerrada ? 'disabled' : '' }}
                            style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;font-family:'Source Sans 3',sans-serif;width:100%;">
                </div>
                @endforeach
            </div>

        </div>

        @if(!$gestionCerrada)
        <div class="btn-row">
            <button type="submit" id="btn-guardar" class="btn btn-primary">💾 Guardar Configuración</button>
        </div>
        @endif
    </form>

    {{-- CERRAR GESTIÓN --}}
    @if($gestionCerrada && !$gestionProcesada)
    @php
        $inscritosSinNotas = DB::table('postulante')
            ->where('estado', 'inscrito')
            ->where('gestion_grupo', $gestion->codigo)
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('examen')
                    ->whereColumn('examen.codigo_postulante', 'postulante.codigo')
                    ->where('examen.nro_examen', 3);
            })
            ->count();
    @endphp
    <div class="section-card" style="border: 1.5px solid #fecaca;">
        <div class="section-label" style="color: var(--rojo);">⚠ Cerrar Gestión</div>
        @if($inscritosSinNotas > 0)
        <div class="warning-box">
            ⚠ Hay <strong>{{ $inscritosSinNotas }} postulantes</strong> con notas incompletas. El cierre puede ejecutarse igualmente.
        </div>
        @endif
        <p style="font-size:13px;color:#555;margin-bottom:16px;">
            Al cerrar la gestión <strong>{{ $gestion->codigo }}</strong> el sistema calculará promedios finales,
            actualizará estados a aprobado/reprobado y asignará carreras. Esta acción no se puede deshacer.
        </p>
        <form method="POST" action="{{ route('parametros.cerrar') }}"
                onsubmit="return confirm('¿Confirma el cierre de la gestión {{ $gestion->codigo }}? Esta acción no se puede deshacer.')">
            @csrf
            <button type="submit" class="btn btn-danger">🔒 Cerrar Gestión {{ $gestion->codigo }}</button>
        </form>
    </div>
    @endif

    {{-- ABRIR NUEVA GESTIÓN --}}
    @if($gestionCerrada && $gestionProcesada)
    <div class="nueva-gestion-card">
        <div class="section-label">🆕 Abrir Nueva Gestión</div>
        <p style="font-size:13px;color:#166534;margin-bottom:16px;">La gestión actual está cerrada. Podés abrir una nueva gestión.</p>
        <form method="POST" action="{{ route('parametros.abrir') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Código de Gestión</label>
                    <input type="text" name="codigo" value="{{ $siguienteCodigo }}" readonly
                        style="background:#f5f7fa;color:#555;cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_ini" required>
                </div>
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" required>
                </div>
                <div class="form-group">
                    <label>Inicio Registro de Notas</label>
                    <input type="date" name="fecha_inicio_notas" required>
                </div>
                <div class="form-group">
                    <label>Fin Registro de Notas</label>
                    <input type="date" name="fecha_fin_notas" required>
                </div>
                <div class="form-group">
                    <label>Nota Mínima</label>
                    <input type="number" name="nota_minima" value="60" min="0" max="100" required>
                </div>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn btn-success">🚀 Abrir Nueva Gestión</button>
            </div>
        </form>
    </div>
    @endif

    @else
    <div class="section-card">
        <p style="text-align:center;color:#888;padding:30px;">No hay ninguna gestión registrada en el sistema.</p>
    </div>
    @endif

</div>

<script>
// Datos de materias y turnos desde PHP
const materiasData = {
    @foreach($materias as $m)
    {{ $m->id }}: { nombre: '{{ $m->nombre }}', duracion: {{ $m->duracion }} },
    @endforeach
};

// Prioridad: mat(1), fis(2), com(4), ing(3)
const prioridad = [1, 2, 4, 3];

function timeToMinutes(t) {
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
}

function minutesToTime(m) {
    m = m % (24 * 60); // limitar a 24h
    const h = Math.floor(m / 60).toString().padStart(2, '0');
    const min = (m % 60).toString().padStart(2, '0');
    return `${h}:${min}`;
}

function getDuraciones() {
    const durs = {};
    prioridad.forEach(id => {
        const el = document.getElementById(`materia_dur_${id}`);
        durs[id] = el ? parseFloat(el.value) || 1.0 : 1.0;
    });
    return durs;
}

function getTurnos() {
    return {
        'mañana': {
            ini: document.getElementById('turno_ini_mañana')?.value || '07:00',
            fin: document.getElementById('turno_fin_mañana')?.value || '11:00',
        },
        'tarde': {
            ini: document.getElementById('turno_ini_tarde')?.value || '13:00',
            fin: document.getElementById('turno_fin_tarde')?.value || '17:00',
        },
        'noche': {
            ini: document.getElementById('turno_ini_noche')?.value || '19:00',
            fin: document.getElementById('turno_fin_noche')?.value || '23:00',
        },
    };
}

function recalcDuracionesPorTurno(nombreTurno) {
    // Si cambió hora_fin del turno → redistribuir duraciones equitativamente
    const turnos = getTurnos();
    const t = turnos[nombreTurno];
    const totalMin = timeToMinutes(t.fin) - timeToMinutes(t.ini);
    if (totalMin <= 0) return;

    const totalHoras = totalMin / 60;
    const base = Math.floor((totalHoras / 4) * 2) / 2; // redondear a 0.5 inferior
    const sobrante = Math.round((totalHoras - base * 3) * 10) / 10;

    prioridad.forEach((id, idx) => {
        const el = document.getElementById(`materia_dur_${id}`);
        if (el) el.value = idx < 3 ? base : Math.max(0.5, sobrante);
    });
}

function actualizarPreview() {
    const turnos   = getTurnos();
    const durs     = getDuraciones();
    const turnList = ['mañana', 'tarde', 'noche'];
    let html = '';
    let hayError = false;

    turnList.forEach((nombre, idx) => {
        const t = turnos[nombre];
        let minActual = timeToMinutes(t.ini);
        prioridad.forEach(id => {
            minActual += (durs[id] || 1) * 60;
        });
        const finStr = minutesToTime(minActual);

        let estado = '✅';
        let claseEstado = 'preview-ok';
        if (idx < turnList.length - 1) {
            const siguienteIni = timeToMinutes(turnos[turnList[idx + 1]].ini);
            if (minActual > siguienteIni) {
                estado = '❌ Choca con ' + turnList[idx + 1];
                claseEstado = 'preview-error';
                hayError = true;
            }
        }

        html += `<div class="preview-row">
            <span class="preview-turno">${nombre.charAt(0).toUpperCase() + nombre.slice(1)}</span>
            <span class="preview-fin">${t.ini} → ${finStr}</span>
            <span class="${claseEstado}">${estado}</span>
        </div>`;
    });

    document.getElementById('preview-content').innerHTML = html;

    const btn = document.getElementById('btn-guardar');
    if (btn) {
        btn.style.opacity = hayError ? '0.5' : '1';
        btn.style.cursor  = hayError ? 'not-allowed' : 'pointer';
        btn.onclick = hayError ? (e) => { e.preventDefault(); alert('Corrige los choques de horario antes de guardar.'); } : null;
    }
}

// Event listeners
document.querySelectorAll('.materia-input').forEach(el => {
    el.addEventListener('input', actualizarPreview);
});

document.querySelectorAll('.turno-input').forEach(el => {
    el.addEventListener('input', function() {
        // Si es hora_fin de algún turno, redistribuir duraciones
        const id = this.id;
        if (id.startsWith('turno_fin_')) {
            const nombreTurno = id.replace('turno_fin_', '');
            recalcDuracionesPorTurno(nombreTurno);
        }
        actualizarPreview();
    });
});

// Inicializar preview al cargar
actualizarPreview();
</script>
@endsection
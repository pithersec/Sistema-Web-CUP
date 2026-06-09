<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Estado - CUP FICCT</title>
    <link rel="stylesheet" href="{{ asset('css/preinscripcion.css') }}">
    <style>
        .container { max-width: 860px; margin: 32px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 20px; }
        .card-header { background: #0d3b6e; padding: 16px 24px; display: flex; align-items: center; gap: 10px; }
        .card-header h2 { font-family: 'Merriweather', serif; color: white; font-size: 15px; }
        .card-body { padding: 24px; }
        .search-box { display: flex; gap: 12px; align-items: center; }
        .search-box input { flex: 1; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 15px; outline: none; font-family: 'Source Sans 3', sans-serif; }
        .search-box input:focus { border-color: #2980b9; }
        .search-box button { padding: 12px 28px; background: #0d3b6e; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Source Sans 3', sans-serif; white-space: nowrap; }
        .search-box button:hover { background: #1a5fa8; }
        .field-error { color: #c0392b; font-size: 13px; margin-top: 8px; }
        .fields-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .fields-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .field-item label { display: block; font-size: 11px; font-weight: 600; color: #5a5a5a; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 4px; }
        .field-item p { font-size: 14px; color: #1a1a1a; font-weight: 500; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .badge-green  { background: #d4f5e2; color: #1a7a3c; }
        .badge-red    { background: #fde8e8; color: #c0392b; }
        .badge-yellow { background: #fef9e7; color: #d68910; }
        .badge-blue   { background: #dceeff; color: #1a5fa8; }
        .badge-dark   { background: #e2e2e2; color: #1a1a1a; }
        .badge-presencial { background: #dbeafe; color: #1D4ED8; }
        .badge-virtual    { background: #cffafe; color: #036d80; }
        .req-item { display: flex; align-items: center; gap: 8px; font-size: 13px; margin-bottom: 8px; }
        .req-check { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; flex-shrink: 0; }
        .req-check.ok { background: #d4f5e2; color: #1a7a3c; }
        .req-check.no { background: #fde8e8; color: #c0392b; }
        .notas-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .notas-table thead { background: #0d3b6e; color: white; }
        .notas-table th { padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600; }
        .notas-table td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; }
        .notas-table tr:last-child td { border-bottom: none; }
        .pago-box { background: #f0f8ff; border: 1.5px solid #dceeff; border-radius: 8px; padding: 16px 20px; }
        .carrera-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; }
        .carrera-opcion { width: 28px; height: 28px; border-radius: 50%; background: #0d3b6e; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
        .carrera-info strong { display: block; font-size: 14px; color: #0d3b6e; }
        .carrera-info span { font-size: 12px; color: #5a5a5a; }
        .btn-pago { display: inline-block; padding: 12px 32px; background: #27ae60; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 16px; }
        .btn-pago:hover { background: #1e8449; }
        .btn-pago.disabled { background: #aaa; cursor: not-allowed; pointer-events: none; }
        @media (max-width: 768px) {
            .fields-grid { grid-template-columns: repeat(2, 1fr); }
            .fields-grid-2 { grid-template-columns: 1fr; }
            .search-box { flex-direction: column; }
            .search-box input, .search-box button { width: 100%; }
        }
        @media (max-width: 480px) {
            .fields-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ url('/') }}" class="topbar-btn">← Volver</a>
        <div class="topbar-left">
            <img src="{{ asset('img/Escudo_FICCT.png') }}" alt="FICCT" style="width:40px; height:40px; object-fit:contain;">
            <div>
                <h1>Consultar Estado de Admisión</h1>
                <p>FICCT · Universidad Autónoma Gabriel René Moreno</p>
            </div>
        </div>
        <div class="topbar-spacer"></div>
    </div>

    <div class="container">

        @if(session('success'))
        <div style="background:#d4f5e2; color:#1a7a3c; padding:14px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:600;">
            ✓ {{ session('success') }}
        </div>
        @endif

        {{-- BÚSQUEDA --}}
        <div class="card">
            <div class="card-header"><h2>🔍 Buscar Postulante</h2></div>
            <div class="card-body">
                <form action="{{ route('estado.consultar') }}" method="POST">
                    @csrf
                    <div class="search-box">
                        <input type="text" name="busqueda"
                            placeholder="Ingresa tu código de postulante o CI"
                            value="{{ old('busqueda') }}" autofocus />
                        <button type="submit">Consultar →</button>
                    </div>
                    @error('busqueda')
                    <p class="field-error">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>

        @isset($postulante)

        {{-- ESTADO Y DATOS DE REGISTRO --}}
        <div class="card">
            <div class="card-header"><h2>📋 Estado de Admisión</h2></div>
            <div class="card-body">
                <div class="fields-grid">
                    <div class="field-item">
                        <label>Código de Postulante</label>
                        <p><strong>{{ $postulante->codigo }}</strong></p>
                    </div>
                    <div class="field-item">
                        <label>Estado</label>
                        <p>
                            @if($postulante->estado == 'aprobado') <span class="badge badge-green">Aprobado</span>
                            @elseif($postulante->estado == 'reprobado') <span class="badge badge-red">Reprobado</span>
                            @elseif($postulante->estado == 'inscrito') <span class="badge badge-blue">Inscrito</span>
                            @elseif($postulante->estado == 'preinscrito') <span class="badge badge-yellow">Preinscrito</span>
                            @elseif($postulante->estado == 'baja') <span class="badge badge-dark">Baja</span>
                            @endif
                        </p>
                    </div>
                    <div class="field-item">
                        <label>Estado del Formulario</label>
                        <p>
                            @if($postulante->estado_formulario == 'activo') <span class="badge badge-green">Activo</span>
                            @elseif($postulante->estado_formulario == 'vencido') <span class="badge badge-red">Vencido</span>
                            @elseif($postulante->estado_formulario == 'anulado') <span class="badge badge-dark">Anulado</span>
                            @endif
                        </p>
                    </div>
                    <div class="field-item">
                        <label>Turno Preferido</label>
                        <p>{{ $postulante->nombre_turno ? ucfirst($postulante->nombre_turno) : 'No especificado' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Grupo Asignado</label>
                        <p>{{ $grupo ? $grupo->id . ' — ' . ucfirst($grupo->nombre_turno) : 'Sin grupo' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Plazo de Validación</label>
                        <p>{{ $postulante->plazo ? \Carbon\Carbon::parse($postulante->plazo)->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                </div>

                @if($postulante->estado_formulario == 'vencido' || $postulante->estado_formulario == 'anulado')
                <div style="background:#fde8e8; border:1.5px solid #f8b4b4; border-radius:8px; padding:14px 16px; margin-top:16px; font-size:13px; color:#c0392b;">
                    ⚠️ Tu formulario está <strong>{{ $postulante->estado_formulario }}</strong>.
                    Puedes generar uno nuevo desde <a href="{{ route('preinscripcion.form') }}" style="color:#c0392b; font-weight:600;">Preinscripción</a>.
                </div>
                @endif
            </div>
        </div>

        {{-- DATOS PERSONALES --}}
        <div class="card">
            <div class="card-header"><h2>👤 Datos Personales</h2></div>
            <div class="card-body">
                <div class="fields-grid">
                    <div class="field-item">
                        <label>CI</label>
                        <p>{{ $postulante->ci }}</p>
                    </div>
                    <div class="field-item">
                        <label>Nombre(s)</label>
                        <p>{{ $postulante->datosPersonales->nombre ?? 'N/A' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Apellido(s)</label>
                        <p>{{ $postulante->datosPersonales->apellido ?? 'N/A' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Género</label>
                        <p>{{ $postulante->datosPersonales->genero == 'm' ? 'Masculino' : 'Femenino' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Fecha de Nacimiento</label>
                        <p>{{ $postulante->datosPersonales->fecha_nac ? \Carbon\Carbon::parse($postulante->datosPersonales->fecha_nac)->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Procedencia</label>
                        <p>{{ $postulante->procedencia ?? 'N/A' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Teléfono</label>
                        <p>{{ $postulante->datosPersonales->telefono ?? 'S/N' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Teléfono 2</label>
                        <p>{{ $postulante->telefono_2 ?? 'S/N' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Correo</label>
                        <p>{{ $postulante->datosPersonales->correo ?? 'N/A' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Dirección</label>
                        <p>{{ $postulante->datosPersonales->direccion ?? 'N/A' }}</p>
                    </div>
                    <div class="field-item">
                        <label>Año de Egreso</label>
                        <p>{{ $postulante->gestion_egreso ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLEGIO --}}
        @if($postulante->colegio)
        <div class="card">
            <div class="card-header"><h2>🏫 Unidad Educativa</h2></div>
            <div class="card-body">
                <div class="fields-grid">
                    <div class="field-item">
                        <label>Nombre</label>
                        <p>{{ $postulante->colegio->nombre }}</p>
                    </div>
                    <div class="field-item">
                        <label>Tipo</label>
                        <p>{{ ucfirst($postulante->colegio->tipo) }}</p>
                    </div>
                    <div class="field-item">
                        <label>Turno</label>
                        <p>{{ ucfirst($postulante->colegio->turno) }}</p>
                    </div>
                    <div class="field-item">
                        <label>Departamento</label>
                        <p>{{ $postulante->colegio->departamento ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- CARRERAS --}}
        @if(isset($carreras) && $carreras->count() > 0)
        <div class="card">
            <div class="card-header"><h2>🎓 Carreras Seleccionadas</h2></div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    @foreach($carreras as $c)
                <div class="carrera-item">
                    <div class="carrera-opcion">{{ $c->opcion }}</div>
                    <div class="carrera-info">
                        <strong>{{ $c->nombre }}</strong>
                        <span>
                            @if($c->modalidad === 'virtual')
                                <span class="badge badge-virtual">Virtual</span>
                            @else
                                <span class="badge badge-presencial">Presencial</span>
                            @endif
                        </span>
                    </div>
                </div>
                @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- REQUISITOS --}}
        <div class="card">
            <div class="card-header"><h2>📄 Requisitos</h2></div>
            <div class="card-body">
                @if($postulante->requisitosPostulante)
                @php $req = $postulante->requisitosPostulante; @endphp
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px;">
                    @foreach([
                        'titulo_original'  => 'Título Original',
                        'titulo_copia'     => 'Copia del Título',
                        'fotocopia_carnet' => 'Fotocopia Carnet',
                        'formulario'       => 'Formulario',
                        'comprobante'      => 'Comprobante de Pago',
                        'libreta'          => 'Libreta Escolar',
                    ] as $campo => $label)
                    <div class="req-item">
                        <div class="req-check {{ $req->$campo ? 'ok' : 'no' }}">{{ $req->$campo ? '✓' : '✗' }}</div>
                        {{ $label }}
                    </div>
                    @endforeach
                </div>
                @else
                <p style="color:#aaa; font-style:italic;">Sin requisitos registrados</p>
                @endif
            </div>
        </div>

        {{-- NOTAS --}}
        @if(isset($examenes) && $examenes->count() > 0)
        <div class="card">
            <div class="card-header"><h2>📝 Notas por Materia</h2></div>
            <div class="card-body" style="padding:0;">
                <table class="notas-table">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Examen</th>
                            <th>Ponderación</th>
                            <th>Nota</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($examenes as $e)
                        <tr>
                            <td>{{ $e->materia }}</td>
                            <td>Examen {{ $e->nro_examen }}</td>
                            <td>{{ $e->ponderacion }}%</td>
                            <td><strong>{{ $e->nota }}</strong></td>
                            <td>
                                @if($e->nota >= 60)
                                    <span class="badge badge-green">Aprobado</span>
                                @else
                                    <span class="badge badge-red">Reprobado</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- PAGO --}}
        <div class="card">
            <div class="card-header"><h2>💳 Pago</h2></div>
            <div class="card-body">
                @if($postulante->pago)
                @php $pago = $postulante->pago; @endphp
                <div class="pago-box">
                    <div class="fields-grid">
                        <div class="field-item">
                            <label>Monto</label>
                            <p>{{ $pago->moneda }} {{ number_format($pago->monto, 2) }}</p>
                        </div>
                        <div class="field-item">
                            <label>Fecha</label>
                            <p>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</p>
                        </div>
                        <div class="field-item">
                            <label>Estado</label>
                            <p><span class="badge badge-green">{{ ucfirst($pago->estado) }}</span></p>
                        </div>
                        <div class="field-item">
                            <label>Concepto</label>
                            <p>{{ $pago->concepto ?? 'N/A' }}</p>
                        </div>
                        <div class="field-item">
                            <label>ID Transacción</label>
                            <p style="font-size:12px; word-break:break-all;">{{ $pago->id_transaccion ?? 'N/A' }}</p>
                        </div>
                        <div class="field-item">
                            <label>Moneda</label>
                            <p>{{ $pago->moneda ?? 'USD' }}</p>
                        </div>
                    </div>
                </div>
                @else
                <p style="color:#aaa; font-style:italic; font-size:13px; margin-bottom:12px;">No has realizado el pago aún.</p>
                @php
                    $req = $postulante->requisitosPostulante;
                    $puedesPagar = $req && $req->titulo_original && $req->titulo_copia &&
                        $req->fotocopia_carnet && $req->formulario && $req->libreta &&
                        $postulante->estado_formulario == 'activo';
                @endphp
                <a href="{{ route('pago.index', $postulante->codigo) }}"
                    class="btn-pago {{ $puedesPagar ? '' : 'disabled' }}">
                    💳 Realizar Pago
                </a>
                @if(!$puedesPagar)
                <p style="font-size:12px; color:#c0392b; margin-top:8px;">Debes completar todos los requisitos antes de realizar el pago.</p>
                @endif
                @endif
            </div>
        </div>

        @endisset
    </div>
</body>
</html>
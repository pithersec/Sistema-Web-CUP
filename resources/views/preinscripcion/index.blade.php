<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preinscripción CUP - FICCT</title>
    <link rel="stylesheet" href="{{ asset('css/preinscripcion.css') }}">
    <style>
        .container { max-width: 860px; margin: 32px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 20px; }
        .card-header { background: #0d3b6e; padding: 16px 24px; display: flex; align-items: center; gap: 10px; }
        .card-header h2 { font-family: 'Merriweather', serif; color: white; font-size: 15px; }
        .card-body { padding: 24px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 12px; }
        label { display: block; font-size: 11px; font-weight: 600; color: #0d3b6e; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; }
        .req { color: #c0392b; font-weight: 700; }
        input, select { width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-family: 'Source Sans 3', sans-serif; font-size: 14px; color: #333; background: white; outline: none; box-sizing: border-box; }
        input:focus, select:focus { border-color: #2980b9; }
        .has-error { border-color: #c0392b !important; background: #fdf0f0; }
        .field-error { color: #c0392b; font-size: 12px; margin-top: 4px; }
        .footer-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .btn-primary { padding: 11px 28px; border: none; border-radius: 6px; background: #0d3b6e; color: white; font-family: 'Source Sans 3', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn-primary:hover { background: #1a5fa8; }
        .alert-danger { background: #fadbd8; color: #78281f; padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; border-left: 4px solid #c0392b; }
        .alert-danger ul { margin: 6px 0 0 18px; }
        .alert-danger li { margin-bottom: 4px; }
        .field-hint { font-size: 11px; color: #aaa; margin-top: 4px; }

        @media (max-width: 768px) {
            .grid-3 { grid-template-columns: 1fr 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    <div class="topbar">
        <a href="{{ url('/') }}" class="topbar-btn">← Volver</a>
        <div class="topbar-left">
            <img src="{{ asset('img/Escudo_FICCT.png') }}" alt="FICCT" style="width:40px; height:40px; object-fit:contain;">
            <div>
                <h1>Preinscripción al Curso Preuniversitario</h1>
                <p>FICCT · Universidad Autónoma Gabriel René Moreno</p>
            </div>
        </div>
        <div class="topbar-spacer"></div>
    </div>

    <div class="container">

        @if($errors->any())
        <div class="alert-danger">
            <strong>Por favor complete los campos obligatorios:</strong>
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('preinscripcion.registrar') }}" method="POST">
            @csrf

            {{-- DATOS PERSONALES --}}
            <div class="card">
                <div class="card-header"><h2>👤 Datos Personales</h2></div>
                <div class="card-body">
                    <div class="grid-3">
                        <div class="form-group">
                            <label>Cédula de Identidad <span class="req">*</span></label>
                            <input type="text" name="ci" placeholder="Ej: 12345678"
                                value="{{ old('ci') }}" maxlength="11"
                                class="{{ $errors->has('ci') ? 'has-error' : '' }}" required />
                            @if($errors->has('ci')) <div class="field-error">{{ $errors->first('ci') }}</div> @endif
                        </div>
                        <div class="form-group">
                            <label>Nombre(s) <span class="req">*</span></label>
                            <input type="text" name="nombre" placeholder="Ej: Carlos"
                                value="{{ old('nombre') }}"
                                class="{{ $errors->has('nombre') ? 'has-error' : '' }}" required />
                            @if($errors->has('nombre')) <div class="field-error">{{ $errors->first('nombre') }}</div> @endif
                        </div>
                        <div class="form-group">
                            <label>Apellido(s) <span class="req">*</span></label>
                            <input type="text" name="apellido" placeholder="Ej: Pérez Ramos"
                                value="{{ old('apellido') }}"
                                class="{{ $errors->has('apellido') ? 'has-error' : '' }}" required />
                            @if($errors->has('apellido')) <div class="field-error">{{ $errors->first('apellido') }}</div> @endif
                        </div>
                    </div>

                    <div class="grid-3" style="margin-top:4px">
                        <div class="form-group">
                            <label>Género <span class="req">*</span></label>
                            <select name="genero" class="{{ $errors->has('genero') ? 'has-error' : '' }}" required>
                                <option value="">-- Seleccione --</option>
                                <option value="m" {{ old('genero') === 'm' ? 'selected' : '' }}>Masculino</option>
                                <option value="f" {{ old('genero') === 'f' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @if($errors->has('genero')) <div class="field-error">{{ $errors->first('genero') }}</div> @endif
                        </div>
                        <div class="form-group">
                            <label>Fecha de Nacimiento <span class="req">*</span></label>
                            <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}"
                                class="{{ $errors->has('fecha_nac') ? 'has-error' : '' }}" required />
                            @if($errors->has('fecha_nac')) <div class="field-error">{{ $errors->first('fecha_nac') }}</div> @endif
                        </div>
                        <div class="form-group">
                            <label>Procedencia <span class="req">*</span></label>
                            <select name="procedencia" class="{{ $errors->has('procedencia') ? 'has-error' : '' }}" required>
                                <option value="">-- Seleccione --</option>
                                @foreach(['Santa Cruz','La Paz','Cochabamba','Oruro','Potosí','Tarija','Beni','Pando','Chuquisaca','Extranjero'] as $dep)
                                <option value="{{ $dep }}" {{ old('procedencia') == $dep ? 'selected' : '' }}>{{ $dep }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('procedencia')) <div class="field-error">{{ $errors->first('procedencia') }}</div> @endif
                        </div>
                    </div>

                    <div class="grid-3" style="margin-top:4px">
                        <div class="form-group">
                            <label>1er Teléfono <span class="req">*</span></label>
                            <input type="text" name="telefono" placeholder="Ej: 75123456"
                                value="{{ old('telefono') }}"
                                class="{{ $errors->has('telefono') ? 'has-error' : '' }}" required />
                            @if($errors->has('telefono')) <div class="field-error">{{ $errors->first('telefono') }}</div> @endif
                        </div>
                        <div class="form-group">
                            <label>2do Teléfono <span style="color:#aaa; font-weight:400">(opcional)</span></label>
                            <input type="text" name="telefono_2" placeholder="Ej: 71234567"
                                value="{{ old('telefono_2') }}" />
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico <span class="req">*</span></label>
                            <input type="email" name="correo" placeholder="correo@ejemplo.com"
                                value="{{ old('correo') }}"
                                class="{{ $errors->has('correo') ? 'has-error' : '' }}" required />
                            @if($errors->has('correo')) <div class="field-error">{{ $errors->first('correo') }}</div> @endif
                        </div>
                    </div>

                    <div class="grid-2" style="margin-top:4px">
                        <div class="form-group">
                            <label>Dirección de Domicilio <span class="req">*</span></label>
                            <input type="text" name="direccion" placeholder="Ej: Av. Busch 2do Anillo"
                                value="{{ old('direccion') }}"
                                class="{{ $errors->has('direccion') ? 'has-error' : '' }}" required />
                            @if($errors->has('direccion')) <div class="field-error">{{ $errors->first('direccion') }}</div> @endif
                        </div>
                        <div class="form-group" style="visibility:hidden">
                            {{-- spacer --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- DATOS DEL COLEGIO --}}
            <div class="card">
                <div class="card-header"><h2>🏫 Unidad Educativa de Egreso</h2></div>
                <div class="card-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Unidad Educativa <span class="req">*</span></label>
                            <select name="id_colegio" class="{{ $errors->has('id_colegio') ? 'has-error' : '' }}" required>
                                <option value="">-- Seleccione su colegio --</option>
                                @foreach($colegios as $colegio)
                                <option value="{{ $colegio->id }}" {{ old('id_colegio') == $colegio->id ? 'selected' : '' }}>
                                    {{ $colegio->nombre }} — {{ ucfirst($colegio->tipo) }} · {{ ucfirst($colegio->turno) }}
                                </option>
                                @endforeach
                            </select>
                            @if($errors->has('id_colegio')) <div class="field-error">{{ $errors->first('id_colegio') }}</div> @endif
                        </div>
                        <div class="form-group">
                            <label>Año de Egreso de Bachiller <span class="req">*</span></label>
                            <select name="gestion_egreso" class="{{ $errors->has('gestion_egreso') ? 'has-error' : '' }}" required>
                                <option value="">-- Seleccione el año --</option>
                                @for($anio = date('Y'); $anio >= 2015; $anio--)
                                <option value="{{ $anio }}" {{ old('gestion_egreso') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                                @endfor
                            </select>
                            @if($errors->has('gestion_egreso')) <div class="field-error">{{ $errors->first('gestion_egreso') }}</div> @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARRERAS --}}
            <div class="card">
                <div class="card-header"><h2>🎓 Selección de Carreras</h2></div>
                <div class="card-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Primera Opción <span class="req">*</span></label>
                            <select name="codigo_carrera1" class="{{ $errors->has('codigo_carrera1') ? 'has-error' : '' }}" required>
                                <option value="">-- Selecciona una carrera --</option>
                                @foreach($carreras as $carrera)
                                <option value="{{ $carrera->codigo }}|{{ $carrera->plan }}|{{ $carrera->modalidad }}"
                                    {{ old('codigo_carrera1') == $carrera->codigo.'|'.$carrera->plan.'|'.$carrera->modalidad ? 'selected' : '' }}>
                                    {{ $carrera->nombre }} {{ $carrera->modalidad === 'virtual' ? '(Virtual)' : '' }}
                                </option>
                                @endforeach
                            </select>
                            @if($errors->has('codigo_carrera1')) <div class="field-error">{{ $errors->first('codigo_carrera1') }}</div> @endif
                        </div>
                        <div class="form-group">
                            <label>Segunda Opción <span class="req">*</span></label>
                            <select name="codigo_carrera2" class="{{ $errors->has('codigo_carrera2') ? 'has-error' : '' }}" required>
                                <option value="">-- Selecciona una carrera --</option>
                                @foreach($carreras as $carrera)
                                <option value="{{ $carrera->codigo }}|{{ $carrera->plan }}|{{ $carrera->modalidad }}"
                                    {{ old('codigo_carrera2') == $carrera->codigo.'|'.$carrera->plan.'|'.$carrera->modalidad ? 'selected' : '' }}>
                                    {{ $carrera->nombre }} {{ $carrera->modalidad === 'virtual' ? '(Virtual)' : '' }}
                                </option>
                                @endforeach
                            </select>
                            @if($errors->has('codigo_carrera2')) <div class="field-error">{{ $errors->first('codigo_carrera2') }}</div> @endif
                        </div>
                    </div>
                    <p class="field-hint" style="margin-top: 8px;">Las opciones deben ser diferentes. Si la primera opción no tiene cupos disponibles, se asignará la segunda.</p>
                </div>
            </div>

            <div class="footer-buttons">
                <button type="submit" class="btn-primary">Finalizar Preinscripción →</button>
            </div>

        </form>
    </div>
</body>
</html>
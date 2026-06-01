<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Preinscripción CUP - FICCT</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Source+Sans+3:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --azul: #0d3b6e;
            --azul-claro: #1a5fa8;
            --celeste: #2980b9;
            --rojo: #c0392b;
            --blanco: #f8f9fc;
            --gris: #5a5a5a;
            --gris-claro: #e2e8f0;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }

        .topbar {
            background: var(--azul);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--rojo);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: var(--rojo);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Merriweather', serif;
            font-size: 11px;
            font-weight: 700;
            color: white;
        }

        .topbar h1 {
            font-family: 'Merriweather', serif;
            color: white;
            font-size: 16px;
        }

        .topbar p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
        }

        .container {
            max-width: 860px;
            margin: 32px auto;
            padding: 0 20px;
        }

        .steps {
            display: flex;
            align-items: center;
            gap: 0;
            background: white;
            border-radius: 10px;
            padding: 20px 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step.active .step-circle {
            background: var(--azul-claro);
            color: white;
        }

        .step.pending .step-circle {
            background: var(--gris-claro);
            color: var(--gris);
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
        }

        .step.active .step-label {
            color: var(--azul);
        }

        .step.pending .step-label {
            color: var(--gris);
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: var(--gris-claro);
            margin: 0 8px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .card-header {
            background: var(--azul);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 {
            font-family: 'Merriweather', serif;
            color: white;
            font-size: 15px;
        }

        .card-body {
            padding: 24px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--azul);
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--gris-claro);
            border-radius: 6px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 14px;
            color: #333;
            background: white;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: var(--celeste);
        }

        .section-divider {
            margin: 20px 0 16px;
            border-top: 1px solid var(--gris-claro);
            padding-top: 16px;
        }

        .section-divider h3 {
            font-size: 13px;
            font-weight: 600;
            color: var(--azul-claro);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Cambiamos la grilla a selects para procesar fácilmente el envío en Laravel */
        .footer-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--gris-claro);
        }

        .btn-secondary {
            padding: 11px 24px;
            border: 1.5px solid var(--gris-claro);
            border-radius: 6px;
            background: white;
            color: var(--gris);
            font-family: 'Source Sans 3', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            padding: 11px 28px;
            border: none;
            border-radius: 6px;
            background: var(--azul);
            color: white;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .alert-danger {
            background: #fadbd8;
            color: #78281f;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="topbar">
        <div class="topbar-left">
            <div class="logo">CUP</div>
            <div>
                <h1>Preinscripción al Curso Preuniversitario</h1>
                <p>FICCT · Universidad Autónoma Gabriel René Moreno</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="steps">
            <div class="step active">
                <div class="step-circle">1</div>
                <div class="step-label">Formulario Digital</div>
            </div>
            <div class="step-line"></div>
            <div class="step pending">
                <div class="step-circle">2</div>
                <div class="step-label">Validación Física</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>📋 Datos Personales del Postulante</h2>
            </div>
            <div class="card-body">

                @if ($errors->any())
                <div class="alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ url('/preinscripcion') }}" method="POST">
                    @csrf

                    <div class="grid-3">
                        <div class="form-group">
                            <label>Cédula de Identidad *</label>
                            <input type="text" name="ci" placeholder="Ej: 12345678" value="{{ old('ci') }}" required />
                        </div>
                        <div class="form-group">
                            <label>Nombre(s) *</label>
                            <input type="text" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}"
                                required />
                        </div>
                        <div class="form-group">
                            <label>Apellido(s) *</label>
                            <input type="text" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}"
                                required />
                        </div>
                    </div>

                    <div class="grid-3" style="margin-top:16px">
                        <div class="form-group">
                            <label>Género *</label>
                            <select name="genero" required>
                                <option value="M" {{ old('genero')=='M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('genero')=='F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Nacimiento *</label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required />
                        </div>
                        <div class="form-group">
                            <label>Teléfono / Celular *</label>
                            <input type="text" name="telefono" placeholder="Ej: 75123456" value="{{ old('telefono') }}"
                                required />
                        </div>
                    </div>

                    <div class="grid-2" style="margin-top:16px">
                        <div class="form-group">
                            <label>Correo Electrónico *</label>
                            <input type="email" name="email" placeholder="correo@ejemplo.com" value="{{ old('email') }}"
                                required />
                        </div>
                        <div class="form-group">
                            <label>Dirección de Domicilio</label>
                            <input type="text" name="direccion" placeholder="Ej: Av. Bush 2do Anillo"
                                value="{{ old('direccion') }}" />
                        </div>
                    </div>

                    <div class="section-divider">
                        <h3>🎓 Selección de Carreras Opcionales</h3>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Primera Opción de Carrera *</label>
                            <select name="id_carrera_1" required>
                                <option value="">-- Selecciona una carrera --</option>
                                @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Segunda Opción de Carrera (Opcional)</label>
                            <select name="id_carrera_2">
                                <option value="">-- Ninguna --</option>
                                @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="footer-buttons">
                        <a href="{{ url('/login') }}" class="btn-secondary">Volver al Login</a>
                        <button type="submit" class="btn-primary">Finalizar Preinscripción ✓</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>

</html>
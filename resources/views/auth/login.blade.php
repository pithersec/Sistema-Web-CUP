<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUP FICCT - Iniciar Sesión</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Source+Sans+3:wght@300;400;600&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                url('{{ asset("img/ficct_.jfif") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 24px 16px;
        }

        .btn-volver-wrap {
            position: fixed;
            top: 20px;
            left: 28px;
            z-index: 10;
        }

        .btn-volver {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border: 1.5px solid rgba(255,255,255,0.35);
            border-radius: 20px;
            backdrop-filter: blur(6px);
            background: rgba(255,255,255,0.08);
            transition: all 0.2s;
        }

        .btn-volver:hover {
            background: rgba(255,255,255,0.18);
            color: white;
        }

        /* ── DESKTOP: tarjeta de dos columnas ── */
        .login-card {
            display: flex;
            width: 820px;
            max-width: 100%;
            min-height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,0.4);
        }

        .left {
            background: var(--azul-claro);
            width: 340px;
            flex-shrink: 0;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .left::before {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.1);
            top: -80px; right: -80px;
        }

        .left::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.08);
            bottom: -60px; left: -60px;
        }

        .logo-circle {
            width: 210px; height: 210px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-img { width: 210px; height: 210px; object-fit: contain; }

        .left h1 {
            font-family: 'Merriweather', serif;
            color: white;
            font-size: 22px;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .left p {
            color: rgba(255,255,255,0.7);
            font-size: 16px;
            line-height: 1.6;
        }

        .divider {
            width: 40px; height: 2px;
            background: var(--rojo);
            margin: 20px auto;
        }

        .sistema-badge {
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 6px 16px;
            color: white;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 16px;
        }

        .right {
            background: var(--blanco);
            flex: 1;
            padding: 52px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right h2 {
            font-family: 'Merriweather', serif;
            color: var(--azul);
            font-size: 26px;
            margin-bottom: 6px;
        }

        .right .subtitle {
            color: var(--gris);
            font-size: 14px;
            margin-bottom: 36px;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--azul);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--gris-claro);
            border-radius: 6px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 16px;
            color: #333;
            background: white;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus { border-color: var(--celeste); }

        .forgot {
            text-align: right;
            margin-top: -12px;
            margin-bottom: 20px;
        }

        .forgot a { font-size: 12px; color: var(--celeste); text-decoration: none; }
        .forgot a:hover { text-decoration: underline; }

        .btn {
            width: 100%;
            padding: 14px;
            background: var(--azul);
            color: white;
            border: none;
            border-radius: 6px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover { background: var(--azul-claro); }

        .footer-text {
            text-align: center;
            font-size: 12px;
            color: var(--gris);
            margin-top: 24px;
        }

        .footer-text span { color: var(--rojo); font-weight: 600; }

        .alert-error {
            background: #fde8e8;
            border: 1px solid #f8b4b4;
            color: #9b1c1c;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* ── MOBILE: tarjeta simple, mismo estilo que welcome ── */
        @media (max-width: 700px) {
            body { padding: 24px 16px; }

            .btn-volver-wrap { top: 12px; left: 12px; }

            /* Ocultar panel izquierdo */
            .left { display: none; }

            .login-card {
                width: 100%;
                max-width: 420px;
                min-height: unset;
                border-radius: 14px;
                flex-direction: column;
            }

            /* Header compacto como card-header del diseño mobile */
            .left-mobile-header {
                display: flex !important;
                background: var(--azul-claro);
                padding: 18px 20px;
                align-items: center;
                gap: 12px;
                border-bottom: 3px solid var(--rojo);
            }

            .left-mobile-header img {
                width: 44px; height: 44px;
                object-fit: contain;
                flex-shrink: 0;
            }

            .left-mobile-header h3 {
                font-family: 'Merriweather', serif;
                color: white;
                font-size: 12px;
                line-height: 1.4;
            }

            .left-mobile-header p {
                color: rgba(255,255,255,0.65);
                font-size: 10px;
                margin-top: 2px;
            }

            .right {
                padding: 28px 20px;
                justify-content: flex-start;
            }

            .right h2 { font-size: 22px; }
        }

        /* El header mobile está oculto en desktop */
        .left-mobile-header { display: none; }
    </style>
</head>
<body>

    <div class="btn-volver-wrap">
        <a href="{{ url('/') }}" class="btn-volver">← Volver</a>
    </div>

    <div class="login-card">

        {{-- Panel izquierdo — solo desktop --}}
        <div class="left">
            <div class="logo-circle">
                <img src="{{ asset('img/escudo_ficct.png') }}" alt="FICCT" class="logo-img">
            </div>
            <h1>Universidad Autónoma Gabriel René Moreno</h1>
            <div class="divider"></div>
            <p>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</p>
            <div class="sistema-badge">Sistema de Admisión</div>
        </div>

        {{-- Header compacto — solo mobile --}}
        <div class="left-mobile-header">
            <img src="{{ asset('img/escudo_ficct.png') }}" alt="FICCT">
            <div>
                <h3>Universidad Autónoma Gabriel René Moreno</h3>
                <p>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</p>
            </div>
        </div>

        <form class="right" action="{{ url('/login') }}" method="POST">
            @csrf

            <h2>Iniciar Sesión</h2>
            <p class="subtitle">Ingresa tus credenciales para acceder al sistema</p>

            @if ($errors->any())
            <div class="alert-error">Usuario o contraseña incorrectos.</div>
            @endif

            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="user_name" placeholder="Nombre de usuario"
                    value="{{ old('user_name') }}" required autofocus />
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required />
            </div>

            <div class="forgot">
                <a href="#">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn">Ingresar al Sistema</button>

            <p class="footer-text">Acceso restringido · <span>Solo personal autorizado</span></p>
        </form>
    </div>

</body>
</html> 
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>CUP FICCT - Iniciar Sesión</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Source+Sans+3:wght@300;400;600&display=swap');

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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* RECOMENDACIÓN: Mueve tu imagen ficct_.jfif a la carpeta public/img/ */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
            url('{{ asset(' img/ficct_.jfif') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            display: flex;
            width: 820px;
            min-height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
        }

        .left {
            background: var(--azul-claro);
            width: 340px;
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
            width: 280px;
            height: 280px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.1);
            top: -80px;
            right: -80px;
        }

        .left::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.08);
            bottom: -60px;
            left: -60px;
        }

        .logo-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--rojo);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-family: 'Merriweather', serif;
            font-size: 18px;
            font-weight: 700;
            color: white;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }

        .left h1 {
            font-family: 'Merriweather', serif;
            color: white;
            font-size: 18px;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .left p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            line-height: 1.6;
        }

        .divider {
            width: 40px;
            height: 2px;
            background: var(--rojo);
            margin: 20px auto;
        }

        .sistema-badge {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 6px 16px;
            color: white;
            font-size: 11px;
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

        .form-group {
            margin-bottom: 20px;
        }

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
            font-size: 15px;
            color: #333;
            background: white;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus {
            border-color: var(--celeste);
        }

        .forgot {
            text-align: right;
            margin-top: -12px;
            margin-bottom: 20px;
        }

        .forgot a {
            font-size: 12px;
            color: var(--celeste);
            text-decoration: none;
        }

        .forgot a:hover {
            text-decoration: underline;
        }

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
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }

        .btn:hover {
            background: var(--azul-claro);
        }

        .footer-text {
            text-align: center;
            font-size: 12px;
            color: var(--gris);
            margin-top: 24px;
        }

        .footer-text span {
            color: var(--rojo);
            font-weight: 600;
        }

        /* Alertas de error */
        .alert-error {
            background: #fde8e8;
            border: 1px solid #f8b4b4;
            color: #9b1c1c;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left">
            <div class="logo-circle">FICCT</div>
            <h1>Universidad Autónoma Gabriel René Moreno</h1>
            <div class="divider"></div>
            <p>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</p>
            <div class="sistema-badge">Sistema CUP</div>
        </div>

        <form class="right" action="{{ url('/login') }}" method="POST">
            @csrf

            <h2>Iniciar Sesión</h2>
            <p class="subtitle">Ingresa tus credenciales para acceder al sistema</p>

            @if ($errors->any())
            <div class="alert-error">
                Usuario o contraseña incorrectos.
            </div>
            @endif

            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="user_name" placeholder="Nombre de usuario" value="{{ old('user_name') }}"
                    required autofocus />
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
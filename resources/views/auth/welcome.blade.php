<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CUP - FICCT</title>
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            justify-content: center;
            padding-top: 40px 16px;
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)),
            url('{{ asset("img/ficct_.jfif") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Acceso personal — esquina superior derecha */
        .acceso-personal {
            position: fixed;
            top: 20px;
            right: 28px;
            z-index: 10;
        }

        .acceso-personal a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 8px 16px;
            border: 1.5px solid rgba(255, 255, 255, 0.35);
            border-radius: 20px;
            backdrop-filter: blur(6px);
            background: rgba(255, 255, 255, 0.08);
            transition: all 0.2s;
        }

        .acceso-personal a:hover {
            background: rgba(255, 255, 255, 0.18);
            color: white;
            border-color: rgba(255, 255, 255, 0.6);
        }

        /* Contenido central */
        .hero {
            text-align: center;
            padding: 24px 24px;
            max-width: 700px;
            width: 100%;
        }

        .escudo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin: 0 auto 24px;
            display: block;
            filter: drop-shadow(0 4px 16px rgba(0, 0, 0, 0.4));
        }

        .hero h1 {
            font-family: 'Merriweather', serif;
            color: white;
            font-size: 17px;
            font-weight: 400;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 1;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
            margin-bottom: 6px;
        }

        .hero h2 {
            font-family: 'Merriweather', serif;
            color: white;
            font-size: 28px;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .hero .facultad {
            color: rgba(255, 255, 255, 0.85);
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            font-size: 16px;
            margin-bottom: 8px;
        }

        .divider {
            width: 48px;
            height: 3px;
            background: var(--rojo);
            margin: 12px auto 16px;
            border-radius: 2px;
        }

        .hero .descripcion {
            color: rgba(255, 255, 255, 0.9);
            font-size: 17px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            line-height: 1.6;
            margin-bottom: 24px;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Tarjetas de acción */
        .cards {
            margin-top: 0;
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 32px 28px;
            width: 260px;
            text-align: center;
            text-decoration: none;
            transition: all 0.25s;
            cursor: pointer;
        }

        .card:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        }

        .card-icon {
            font-size: 42px;
            margin-bottom: 16px;
            display: block;
        }

        .card h3 {
            font-family: 'Merriweather', serif;
            color: white;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .card-btn {
            display: inline-block;
            padding: 10px 24px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-decoration: none;
            transition: background 0.2s;
        }

        .card-primary .card-btn {
            background: var(--rojo);
            color: white;
        }

        .card-primary .card-btn:hover {
            background: #a93226;
        }

        .card-primary {
            border-color: rgba(192, 57, 43, 0.5);
        }

        .card-secondary .card-btn {
            background: var(--azul-claro);
            color: white;
        }

        .card-secondary .card-btn:hover {
            background: var(--azul);
        }

        /* Footer */
        .footer {
            position: relative;
            bottom: 16px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        @media (max-width: 600px) {
            .footer {
                position: relative;
                bottom: auto;
                margin-top: 24px;
                padding: 12px 0 20px;
            }

            .acceso-personal {
                position: relative;
                top: auto;
                right: auto;
                text-align: center;
                margin-bottom: 16px;
            }

            .hero h2 {
                font-size: 20px;
            }

            .cards {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 100%;
                max-width: 340px;
            }
        }

        .reclamo-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            width: 100%;
            max-width: 530px;
            padding: 1px;
            margin: 20px auto;
            text-align: center;
        }

        .reclamo-card h2 {
            color: white;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .reclamo-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 15px;
            margin-bottom: 15px;
        }

        .reclamo-btn {
            display: inline-block;
            background: orange;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <!-- Acceso personal esquina superior derecha -->
    <div class="acceso-personal">
        <a href="{{ url('/login') }}">
            🔐 Acceso Personal FICCT
        </a>
    </div>

    <!-- Contenido principal -->
    <div class="hero">
        <img src="{{ asset('img/Escudo_FICCT.png') }}" alt="FICCT" class="escudo">

        <h1>Universidad Autónoma Gabriel René Moreno</h1>
        <h2>Sistema CUP FICCT</h2>
        <p class="facultad">Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</p>

        <div class="divider"></div>

        <p class="descripcion">
            Plataforma de gestión del Curso Preuniversitario. Si eres un nuevo postulante, inicia tu proceso de
            preinscripción aquí.
        </p>

        <div class="cards">
            <a href="{{ url('/preinscripcion') }}" class="card card-primary">
                <span class="card-icon">📋</span>
                <h3>Preinscripción</h3>
                <p>¿Eres un nuevo postulante? Completa tu formulario de preinscripción en línea.</p>
                <span class="card-btn">Iniciar ahora</span>
            </a>

            <a href="{{ route('estado.form') }}" class="card card-secondary">
                <span class="card-icon">🎓</span>
                <h3>Consultar Estado</h3>
                <p>¿Ya te preinscribiste? Consulta el estado de tu admisión con tu código.</p>
                <span class="card-btn">Consultar</span>
            </a>
        </div>
        <div class="reclamo-card">
            <div>
                <div style="font-size:10px;padding:0;margin:0;">⚠️</div>
                <h2 style="font-size:10px;padding:0;margin:0;">Presentar Reclamo</h2>
                <p style="font-size:10px;padding:0;margin:0;">¿Tienes algún inconveniente con tu proceso? Envía un
                    reclamo
                    formal aquí.
                </p>
            </div>
            <a href="{{ route('reclamos.create') }}"
                class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-4 rounded-xl transition duration-200 text-center uppercase tracking-wider text-sm"
                style="font-size:10px; padding:1px 1px;color:rgb(0, 234, 255);">
                REDACTAR RECLAMO
            </a>
        </div>

    </div>

    <p class="footer">FICCT · UAGRM · Sistema CUP © 2026</p>

</body>

</html>
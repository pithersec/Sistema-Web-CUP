<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro Exitoso - CUP</title>
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
            --rojo: #c0392b;
            --blanco: #f8f9fc;
            --gris: #5a5a5a;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        .icon {
            font-size: 50px;
            color: #27ae60;
            margin-bottom: 16px;
        }

        h2 {
            font-family: 'Merriweather', serif;
            color: var(--azul);
            font-size: 24px;
            margin-bottom: 12px;
        }

        p {
            color: var(--gris);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .info-box {
            background: #f8f9fc;
            padding: 16px;
            border-radius: 8px;
            border-left: 4px solid var(--azul-claro);
            text-align: left;
            margin-bottom: 24px;
        }

        .info-box p {
            margin-bottom: 6px;
            font-size: 14px;
        }

        .info-box strong {
            color: var(--azul);
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--azul);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .btn:hover {
            background: var(--azul-claro);
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">✓</div>
        <h2>{{ session('success') }}</h2>
        <p>Tu solicitud digital ha sido procesada por la plataforma de admisiones de la facultad.</p>

        <div class="info-box">
            <p>Código de Postulante: <strong>{{ session('codigo_postulante') }}</strong></p>
            <p>Plazo límite de validación: <strong>{{ session('plazo_limite') }}</strong></p>
            <p>Estado del Trámite: <strong style="color: #e67e22; text-transform: uppercase;">Preinscrito</strong></p>
        </div>

        <p style="font-size: 13px; color: #7f8c8d;">Presenta tu documento de identidad y comprobantes en las ventanillas
            de la FICCT antes de la fecha límite para formalizar tu inscripción.</p>

        <a href="{{ url('/preinscripcion') }}" class="btn">Volver al Inicio</a>
    </div>
</body>

</html>
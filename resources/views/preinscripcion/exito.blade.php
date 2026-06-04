<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preinscripción Exitosa - CUP</title>
    <link rel="stylesheet" href="{{ asset('css/preinscripcion.css') }}">
    <style>
        body { display: flex; flex-direction: column; }
        .content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .card { background: white; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); padding: 52px 48px; text-align: center; max-width: 480px; width: 100%; }
        .check { font-size: 64px; color: #10b981; margin-bottom: 20px; }
        h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 22px; margin-bottom: 16px; }
        p { color: #5a5a5a; font-size: 15px; margin-bottom: 12px; line-height: 1.6; }
        .codigo { background: #f0f4f8; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 14px 20px; margin: 20px 0; font-size: 20px; font-weight: 700; color: #0d3b6e; letter-spacing: 2px; }
        .btn { display: inline-block; padding: 12px 32px; background: #0d3b6e; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 15px; margin-top: 8px; }
        .btn:hover { background: #1a5fa8; }
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
    <div class="content">
        <div class="card">
            <div class="check">✓</div>
            <h2>¡Preinscripción registrada con éxito!</h2>
            <p>Guarda tu código de postulante, lo necesitarás para consultar tu estado de admisión.</p>
            <div class="codigo" id="codigoPostulante">{{ session('codigo_postulante') }}</div>
            <button onclick="copiarCodigo()" id="btnCopiar" style="background:none; border:none; color:#1a5fa8; font-size:13px; cursor:pointer; margin-top:-8px; margin-bottom:12px;">📋 Copiar código</button>
            <p>Fecha límite de validación física: <strong>{{ session('plazo_limite') }}</strong></p>
            <a href="{{ url('/') }}" class="btn">Ir al Inicio</a>
        </div>
    </div>
<script>
function copiarCodigo() {
    const texto = document.getElementById('codigoPostulante').innerText;
    navigator.clipboard.writeText(texto);
    document.getElementById('btnCopiar').innerText = '✓ Copiado';
    setTimeout(() => document.getElementById('btnCopiar').innerText = '📋 Copiar código', 2000);
}
</script>
</body>
</html>
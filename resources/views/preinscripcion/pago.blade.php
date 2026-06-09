<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago - CUP</title>
    <link rel="stylesheet" href="{{ asset('css/preinscripcion.css') }}">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 40px 48px;
            max-width: 480px;
            width: 100%;
        }
        .pago-header { text-align: center; margin-bottom: 28px; }
        .pago-icon { font-size: 48px; margin-bottom: 12px; }
        h2 { font-family: 'Merriweather', serif; color: #0d3b6e; font-size: 20px; margin-bottom: 8px; }
        .pago-header p { color: #5a5a5a; font-size: 14px; }

        .resumen {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .resumen-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .resumen-row:last-child { border-bottom: none; }
        .resumen-row .label { color: #5a5a5a; font-size: 13px; }
        .resumen-row .value { color: #1a1a1a; font-weight: 600; }
        .resumen-row.total .label { font-weight: 700; color: #0d3b6e; font-size: 15px; }
        .resumen-row.total .value { font-size: 22px; color: #0d3b6e; font-family: 'Merriweather', serif; }

        .requisitos-warning {
            background: #fef3c7;
            border: 1.5px solid #fde68a;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #92400e;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .btn-pagar {
            width: 100%;
            padding: 14px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-bottom: 12px;
        }
        .btn-pagar:hover { background: #219a52; }
        .btn-pagar:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        .btn-volver {
            width: 100%;
            padding: 11px;
            background: white;
            color: #5a5a5a;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
        }
        .btn-volver:hover { background: #f8fafc; }

        .badge-ok  { background: #d4f5e2; color: #1a7a3c; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .badge-no  { background: #fde8e8; color: #c0392b; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }

        @media (max-width: 480px) {
            .card { padding: 28px 20px; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ url('/') }}" class="topbar-btn">← Volver</a>
        <div class="topbar-left">
            <img src="{{ asset('img/Escudo_FICCT.png') }}" alt="FICCT" style="width:40px; height:40px; object-fit:contain;">
            <div>
                <h1>Pago de Inscripción CUP</h1>
                <p>FICCT · Universidad Autónoma Gabriel René Moreno</p>
            </div>
        </div>
        <div class="topbar-spacer"></div>
    </div>

    <div class="content">
        <div class="card">
            @if($postulante->pago)
            <div style="background:#d4f5e2; border:1.5px solid #10b981; border-radius:8px; padding:14px 16px; margin-bottom:20px; font-size:13px; color:#065f46;">
                ✓ Ya realizaste tu pago el {{ \Carbon\Carbon::parse($postulante->pago->fecha)->format('d/m/Y') }}. ID: {{ $postulante->pago->id_transaccion }}
            </div>
            @endif
            <div class="pago-header">
                <div class="pago-icon">💳</div>
                <h2>Pago de Inscripción</h2>
                <p>Revisa el resumen antes de confirmar tu pago.</p>
            </div>

            {{-- Advertencia si faltan requisitos --}}
            @if(!$requisitosCompletos)
            <div class="requisitos-warning">
                <span>⚠️</span>
                <div>
                    <strong>Requisitos pendientes</strong><br>
                    Aún no has completado todos los requisitos documentales. Debes presentarlos en ventanilla antes de realizar el pago.
                </div>
            </div>
            @endif

            {{-- Resumen --}}
            <div class="resumen">
                <div class="resumen-row">
                    <span class="label">Postulante</span>
                    <span class="value">{{ $postulante->datosPersonales->nombre }} {{ $postulante->datosPersonales->apellido }}</span>
                </div>
                <div class="resumen-row">
                    <span class="label">Código</span>
                    <span class="value">{{ $postulante->codigo }}</span>
                </div>
                <div class="resumen-row">
                    <span class="label">Concepto</span>
                    <span class="value">Inscripción CUP FICCT</span>
                </div>
                <div class="resumen-row">
                    <span class="label">Requisitos</span>
                    <span class="value">
                        @if($requisitosCompletos)
                            <span class="badge-ok">✓ Completos</span>
                        @else
                            <span class="badge-no">✗ Incompletos</span>
                        @endif
                    </span>
                </div>
                <div class="resumen-row total">
                    <span class="label">Total a Pagar</span>
                    <span class="value">{{ $moneda }} 700.00</span>
                </div>
            </div>

            {{-- Formulario de pago --}}
            <form action="{{ route('pago.iniciar', $postulante->codigo) }}" method="POST">
                @csrf
                <button type="submit" class="btn-pagar" {{ !$requisitosCompletos ? 'disabled' : '' }}>
                    ✓ Confirmar Pago — {{ $moneda }} 700.00
                </button>
            </form>

            <a href="{{ route('estado.form', ['busqueda' => $postulante->codigo]) }}" class="btn-volver">← Volver a Consultar Estado</a>
        </div>
    </div>
</body>
</html>

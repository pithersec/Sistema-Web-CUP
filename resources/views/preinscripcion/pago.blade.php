<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago - CUP</title>
    <link rel="stylesheet" href="{{ asset('css/preinscripcion.css') }}">
    <script src="https://js.stripe.com/v3/"></script>
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

        /* Stripe Elements */
        .stripe-section { margin-bottom: 20px; }
        .stripe-label {
            font-size: 13px;
            font-weight: 600;
            color: #0d3b6e;
            margin-bottom: 8px;
            display: block;
        }
        #card-element {
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            transition: border-color 0.2s;
        }
        #card-element.StripeElement--focus { border-color: #1a5fa8; }
        #card-element.StripeElement--invalid { border-color: #c0392b; }
        #card-errors {
            color: #c0392b;
            font-size: 12px;
            margin-top: 6px;
            min-height: 18px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-pagar:hover:not(:disabled) { background: #219a52; }
        .btn-pagar:disabled { background: #aaa; cursor: not-allowed; }

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

        .badge-ok { background: #d4f5e2; color: #1a7a3c; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .badge-no { background: #fde8e8; color: #c0392b; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .stripe-badge {
            text-align: center;
            margin-top: 14px;
            font-size: 11px;
            color: #aaa;
        }

        @media (max-width: 480px) {
            .card { padding: 28px 20px; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ url('/') }}" class="topbar-btn">← Volver</a>
        <div class="topbar-left">
            <img src="{{ asset('img/Escudo_FICCT.png') }}" alt="FICCT" style="width:40px;height:40px;object-fit:contain;">
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
            <div style="background:#d4f5e2;border:1.5px solid #10b981;border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#065f46;">
                ✓ Ya realizaste tu pago el {{ \Carbon\Carbon::parse($postulante->pago->fecha)->format('d/m/Y') }}. ID: {{ $postulante->pago->id_transaccion }}
            </div>
            @endif

            <div class="pago-header">
                <div class="pago-icon">💳</div>
                <h2>Pago de Inscripción</h2>
                <p>Ingresá los datos de tu tarjeta para completar el pago.</p>
            </div>

            @if(!$requisitosCompletos)
            <div class="requisitos-warning">
                <span>⚠️</span>
                <div>
                    <strong>Requisitos pendientes</strong><br>
                    Aún no completaste todos los requisitos documentales. Debés presentarlos en ventanilla antes de realizar el pago.
                </div>
            </div>
            @endif

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
                    <span class="value">Inscripción CUP {{ $postulante->gestion_grupo ?? '' }}</span>
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

            {{-- Formulario Stripe Elements --}}
            @if(!$postulante->pago && $requisitosCompletos)
            <div class="stripe-section">
                <label class="stripe-label">Datos de tarjeta</label>
                <div id="card-element"></div>
                <div id="card-errors"></div>
            </div>

            <button id="btn-pagar" class="btn-pagar">
                <span id="btn-text">🔒 Pagar {{ $moneda }} 700.00</span>
                <div class="spinner" id="spinner"></div>
            </button>
            @endif

            <a href="{{ route('estado.form', ['busqueda' => $postulante->codigo]) }}" class="btn-volver">← Volver a Consultar Estado</a>

            <div class="stripe-badge">🔒 Pago seguro procesado por Stripe</div>
        </div>
    </div>

    <script>
        const stripe = Stripe('{{ $stripePublicKey }}');
        const elements = stripe.elements();

        const cardElement = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    fontFamily: 'Source Sans 3, sans-serif',
                    fontSize: '15px',
                    color: '#1a1a1a',
                    '::placeholder': { color: '#aaa' },
                },
                invalid: { color: '#c0392b' },
            }
        });

        cardElement.mount('#card-element');

        // Mostrar errores en tiempo real
        cardElement.on('change', ({ error }) => {
            document.getElementById('card-errors').textContent = error ? error.message : '';
        });

        // Click en pagar
        document.getElementById('btn-pagar').addEventListener('click', async () => {
            const btn     = document.getElementById('btn-pagar');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btn-text');

            // Mostrar loading
            btn.disabled  = true;
            spinner.style.display = 'block';
            btnText.style.display = 'none';

            try {
                // 1. Pedir client_secret al servidor
                const res = await fetch('{{ route('pago.iniciar', $postulante->codigo) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({}),
                });

                const { client_secret, error: serverError } = await res.json();

                if (serverError) {
                    document.getElementById('card-errors').textContent = serverError;
                    btn.disabled = false;
                    spinner.style.display = 'none';
                    btnText.style.display = 'inline';
                    return;
                }

                // 2. Confirmar pago con Stripe JS
                const { paymentIntent, error: stripeError } = await stripe.confirmCardPayment(client_secret, {
                    payment_method: { card: cardElement }
                });

                if (stripeError) {
                    document.getElementById('card-errors').textContent = stripeError.message;
                    btn.disabled = false;
                    spinner.style.display = 'none';
                    btnText.style.display = 'inline';
                    return;
                }

                // 3. Avisar al servidor que el pago fue exitoso
                const confirm = await fetch('{{ route('pago.exitoso') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ payment_intent_id: paymentIntent.id }),
                });

                const result = await confirm.json();

                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    document.getElementById('card-errors').textContent = result.error ?? 'Error al confirmar el pago.';
                    btn.disabled = false;
                    spinner.style.display = 'none';
                    btnText.style.display = 'inline';
                }

            } catch (e) {
                document.getElementById('card-errors').textContent = 'Error de conexión. Intentá nuevamente.';
                btn.disabled = false;
                spinner.style.display = 'none';
                btnText.style.display = 'inline';
            }
        });
    </script>
</body>
</html>
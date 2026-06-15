<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    // =========================================================
    // CU-04: mostrarPago()
    // Muestra la vista con el formulario de Stripe Elements
    // =========================================================
    public function mostrarPago($codigo)
    {
        $postulante = Postulante::with(['datosPersonales', 'requisitosPostulante', 'pago'])
            ->where('codigo', $codigo)->firstOrFail();

        if ($postulante->pago && $postulante->pago->estado === 'completado') {
            return redirect()->route('estado.form', ['busqueda' => $codigo])
                ->with('success', 'Tu pago ya fue procesado.');
        }

        $req = $postulante->requisitosPostulante;
        $requisitosCompletos = $req &&
            $req->titulo_original && $req->titulo_copia &&
            $req->fotocopia_carnet && $req->formulario &&
            $req->libreta;

        $moneda        = env('PAYMENT_CURRENCY', 'USD');
        $stripePublicKey = config('services.stripe.key');

        return view('preinscripcion.pago', compact(
            'postulante', 'requisitosCompletos', 'moneda', 'stripePublicKey'
        ));
    }

    // =========================================================
    // CU-04: iniciarPago()
    // Llamado via AJAX — crea un PaymentIntent y devuelve
    // el client_secret al frontend para que Stripe JS procese
    // =========================================================
    public function iniciarPago(Request $request, $codigo)
    {
        $postulante = Postulante::where('codigo', $codigo)->firstOrFail();

        // Evitar doble pago
        if ($postulante->id_pago) {
            return response()->json(['error' => 'Este postulante ya realizó su pago.'], 400);
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $intent = \Stripe\PaymentIntent::create([
            'amount'               => 70000, // 700.00 en centavos
            'currency'             => strtolower(env('PAYMENT_CURRENCY', 'USD')),
            'payment_method_types' => ['card'],
            'metadata'             => [
                'codigo_postulante' => $postulante->codigo,
            ],
        ]);

        return response()->json(['client_secret' => $intent->client_secret]);
    }

    // =========================================================
    // CU-04: pagoExitoso()
    // Llamado via AJAX después de que Stripe JS confirma el pago
    // Recibe el payment_intent_id, verifica con Stripe y guarda en BD
    // =========================================================
    public function pagoExitoso(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $intent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

        if ($intent->status !== 'succeeded') {
            return response()->json(['error' => 'El pago no fue completado.'], 400);
        }

        $codigo     = $intent->metadata->codigo_postulante;
        $postulante = Postulante::with(['requisitosPostulante'])
            ->where('codigo', $codigo)->firstOrFail();

        // Definir aquí para usar en concepto y en el redirect
        $codigoGestion = substr($postulante->codigo, 0, 1) . '-' . substr($postulante->codigo, 1, 4);

        if (!$postulante->id_pago) {
            DB::transaction(function () use ($postulante, $intent, $codigoGestion) {
                $pago = DB::table('pago')->insertGetId([
                    'monto'          => 700.00,
                    'fecha'          => now(),
                    'concepto'       => 'Inscripción CUP ' . $codigoGestion,
                    'estado'         => 'completado',
                    'id_transaccion' => $intent->id,
                    'moneda'         => strtoupper(env('PAYMENT_CURRENCY', 'USD')),
                ]);

                $postulante->update([
                    'id_pago' => $pago,
                    'estado'  => 'inscrito',
                ]);

                if ($postulante->requisitosPostulante) {
                    $postulante->requisitosPostulante->update([
                        'comprobante' => true,
                    ]);
                }
            });
        }

        return response()->json([
            'success'  => true,
            'redirect' => route('estado.form', [
                'busqueda' => $codigo,
                'msg'      => '¡Pago completado! Ya estás inscrito en el CUP ' . $codigoGestion . '.',
            ]),
        ]);
    }

    // =========================================================
    // CU-04: pagoWebhook()
    // Respaldo de seguridad — Stripe llama esto directamente
    // por si el usuario cierra el browser antes de que el JS
    // llame a pagoExitoso()
    // =========================================================
    public function pagoWebhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response('Webhook error', 400);
        }

        // Ahora escuchamos payment_intent.succeeded en vez de checkout.session.completed
        if ($event->type === 'payment_intent.succeeded') {
            $intent     = $event->data->object;
            $codigo     = $intent->metadata->codigo_postulante ?? null;
            $postulante = Postulante::with(['requisitosPostulante'])
                ->where('codigo', $codigo)->first();

            if ($postulante && !$postulante->id_pago) {
                $codigoGestion = substr($postulante->codigo, 0, 1) . '-' . substr($postulante->codigo, 1, 4);
            
                DB::transaction(function () use ($postulante, $intent) {
                    $pago = DB::table('pago')->insertGetId([
                        'monto'          => 700.00,
                        'fecha'          => now(),
                        'concepto'       => 'Inscripción CUP ' . ($postulante->gestion_grupo ?? ''),
                        'estado'         => 'completado',
                        'id_transaccion' => $intent->id,
                        'moneda'         => strtoupper(env('PAYMENT_CURRENCY', 'USD')),
                    ]);

                    $postulante->update([
                        'id_pago' => $pago,
                        'estado'  => 'inscrito',
                    ]);

                    if ($postulante->requisitosPostulante) {
                        $postulante->requisitosPostulante->update([
                            'comprobante' => true,
                        ]);
                    }
                });
            }
        }

        return response('OK', 200);
    }
}
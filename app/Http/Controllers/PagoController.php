<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function mostrarPago($codigo)
    {
        $postulante = Postulante::with(['datosPersonales', 'requisitosPostulante'])
            ->where('codigo', $codigo)->firstOrFail();

        // Verificar si ya pagó
        if ($postulante->pago && $postulante->pago->estado === 'completado') {
            return redirect()->route('preinscripcion.exito')
                ->with('codigo_postulante', $codigo)
                ->with('success', 'Tu pago ya fue procesado.');
        }

        // Verificar requisitos completos
        $req = $postulante->requisitosPostulante;
        $requisitosCompletos = $req &&
            $req->titulo_original && $req->titulo_copia &&
            $req->fotocopia_carnet && $req->formulario &&
            $req->libreta;

        $moneda = env('PAYMENT_CURRENCY', 'USD');

        return view('preinscripcion.pago', compact('postulante', 'requisitosCompletos', 'moneda'));
    }

    public function iniciarPago(Request $request, $codigo)
    {
        $postulante = Postulante::with(['datosPersonales', 'requisitosPostulante', 'pago'])
            ->where('codigo', $codigo)->firstOrFail();

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => strtolower(env('PAYMENT_CURRENCY', 'USD')),
                    'product_data' => [
                        'name'        => 'Inscripción CUP ' . ($postulante->gestion_grupo ?? ''),
                        'description' => 'Gestión ' . ($postulante->gestion_grupo ?? ''),
                    ],
                    'unit_amount' => 70000, // 700.00 en centavos
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'codigo_postulante' => $postulante->codigo,
            ],
            'success_url' => route('pago.exitoso') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('pago.index', $codigo),
        ]);

        return redirect($session->url);
    }

    public function pagoExitoso(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::retrieve($request->get('session_id'));

        if ($session->payment_status !== 'paid') {
            return redirect()->route('welcome')->with('error', 'El pago no fue completado.');
        }

        $codigo = $session->metadata->codigo_postulante;
        $postulante = Postulante::where('codigo', $codigo)->firstOrFail();

        if (!$postulante->id_pago) {
            $pago = DB::table('pago')->insertGetId([
                'monto'          => 700.00,
                'fecha'          => now(),
                'concepto'       => 'Inscripción CUP ' . ($postulante->gestion_grupo ?? ''),
                'estado'         => 'completado',
                'id_transaccion' => $session->payment_intent,
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
        }

        return redirect()->route('estado.form', ['busqueda' => $codigo])
            ->with('success', '¡Pago completado! Ya estás inscrito en el CUP.');
    }

    public function pagoWebhook(Request $request)
    {
        $payload = $request->getContent();
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

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $codigo = $session->metadata->codigo_postulante;
            $postulante = Postulante::with(['requisitosPostulante'])
                ->where('codigo', $codigo)->first();

            if ($postulante && !$postulante->id_pago) {
                $pago = DB::table('pago')->insertGetId([
                    'monto'          => 700.00,
                    'fecha'          => now(),
                    'concepto'       => 'Inscripción CUP ' . ($postulante->gestion_grupo ?? ''),
                    'estado'         => 'completado',
                    'id_transaccion' => $session->payment_intent,
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
            }
        }

        return response('OK', 200);
    }
}

<?php

namespace Paymenter\Extensions\Gateways\MercadoPago;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Setting;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Http\Request;

#[ExtensionMeta(
    name: 'Mercado Pago',
    description: 'Integración avanzada multimoneda de Mercado Pago para Paymenter.',
    version: '2.1.0',
    author: 'lorddarz',
    url: 'https://vefixy.com',
    icon: 'https://http2.mlstatic.com/D_NQ_NP_615009-MLA84621338592_052025-N.jpg'
)]
class MercadoPago extends Gateway
{
    public function getConfig($values = [])
    {
        return [
            ['name' => 'access_token', 'label' => 'Access Token', 'type' => 'text', 'required' => true],
            ['name' => 'public_key', 'label' => 'Public Key', 'type' => 'text', 'required' => true],
            ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'required' => true],
            ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'required' => true],
            ['name' => 'webhook_secret', 'label' => 'Webhook Secret', 'type' => 'text', 'required' => false],
            [
                'name' => 'test_mode',
                'label' => 'Modo de Operación',
                'type' => 'select',
                'required' => true,
                'options' => ['0' => 'Producción', '1' => 'Sandbox'],
            ],
        ];
    }

    public function pay(Invoice $invoice, $total)
    {
        MercadoPagoConfig::setAccessToken($this->config('access_token'));
        $client = new PreferenceClient();

        // Return URLs and Webhook
        $baseUrl = url('/');
        $returnUrl = $baseUrl . '/invoices/' . $invoice->id;
        $webhookUrl = $baseUrl . '/extensions/mercadopago/webhook';

        // AUTOMATIC CURRENCY DETECTION (ARS, BRL, MXN, etc.)
        $currency = Setting::where('key', 'settings::currency')->first()->value ?? 'ARS';

        try {
            $preference = $client->create([
                "items" => [[
                    "id" => (string)$invoice->id,
                    "title" => "Factura #" . ($invoice->number ?? $invoice->id),
                    "quantity" => 1,
                    "unit_price" => round((float)$total, 2),
                    "currency_id" => $currency // Now it's dynamic according to the site's config
                ]],
                "back_urls" => [
                    "success" => $returnUrl,
                    "failure" => $returnUrl,
                    "pending" => $returnUrl,
                ],
                "auto_return" => "approved",
                "notification_url" => $webhookUrl,
                "external_reference" => (string)$invoice->id,
            ]);

            // Selects the start point according to the mode (Sandbox or Production)
            $redirectUrl = ($this->config('test_mode') == '1') ? $preference->sandbox_init_point : $preference->init_point;

            return $redirectUrl;

        } catch (\Exception $e) {
            \Log::error('MP ERROR: ' . $e->getMessage());
            return $returnUrl . '?error=' . urlencode($e->getMessage());
        }
    }

    public function webhook(Request $request)
    {
        MercadoPagoConfig::setAccessToken($this->config('access_token'));
        
        // Mercado Pago sends the ID in different ways depending on the event version
        $id = $request->input('data_id') ?? $request->input('id');
        $type = $request->input('type');

        if ($type === 'payment' && $id) {
            try {
                $paymentClient = new \MercadoPago\Client\Payment\PaymentClient();
                $payment = $paymentClient->get($id);

                if ($payment->status === 'approved') {
                    ExtensionHelper::addPayment(
                        $payment->external_reference, 
                        'Mercado Pago', 
                        $payment->transaction_amount, 
                        null, 
                        $payment->id
                    );
                }
            } catch (\Exception $e) {
                \Log::error('MP Webhook Error: ' . $e->getMessage());
            }
        }
        return response()->json(['status' => 'success'], 200);
    }
}
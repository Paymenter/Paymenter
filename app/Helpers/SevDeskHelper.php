<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class SevDeskHelper
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://my.sevdesk.de/api/v1/']);
        $this->apiKey = config('services.sevdesk.api_key');
    }

    public function createInvoice($invoiceData)
    {
        $response = $this->client->post('Invoice', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $invoiceData,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function updateInvoice($invoiceId, $invoiceData)
    {
        $response = $this->client->put("Invoice/{$invoiceId}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $invoiceData,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteInvoice($invoiceId)
    {
        $response = $this->client->delete("Invoice/{$invoiceId}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getInvoice($invoiceId)
    {
        $response = $this->client->get("Invoice/{$invoiceId}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function mapLocalToSevDeskInvoice($localInvoice)
    {
        // Map local invoice data to SevDesk invoice data
        return [
            'contact' => [
                'id' => $localInvoice->user->sevdesk_contact_id,
            ],
            'invoiceDate' => $localInvoice->created_at->format('Y-m-d'),
            'dueDate' => $localInvoice->due_at->format('Y-m-d'),
            'status' => $localInvoice->status,
            'invoiceItems' => $localInvoice->items->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'description' => $item->description,
                ];
            })->toArray(),
        ];
    }
}

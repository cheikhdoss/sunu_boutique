<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PayDunyaService
{
    private $masterKey;
    private $privateKey;
    private $publicKey;
    private $token;
    private $baseUrl;
    private $mode;

    public function __construct()
    {
        $this->masterKey = config('services.paydunya.master_key');
        $this->privateKey = config('services.paydunya.private_key');
        $this->publicKey = config('services.paydunya.public_key');
        $this->token = config('services.paydunya.token');
        $this->baseUrl = config('services.paydunya.base_url', 'https://app.paydunya.com');
        $this->mode = config('services.paydunya.mode', 'test');
    }

    /**
     * Créer une facture PayDunya
     */
    public function createInvoice(array $data)
    {
        try {
            // Structure selon la documentation PayDunya
            $payload = [
                'invoice' => [
                    'total_amount' => $data['amount'],
                    'description' => $data['description'] ?? 'Commande SunuBoutique',
                ],
                'store' => [
                    'name' => config('app.name', 'SunuBoutique'),
                    'tagline' => 'Votre boutique en ligne',
                    'phone' => '+221 XX XXX XX XX',
                    'postal_address' => 'Dakar, Sénégal',
                    'website_url' => config('app.url'),
                    'logo_url' => config('app.url') . '/logo.png',
                ],
                'custom_data' => [
                    'order_id' => $data['order_id'],
                ],
                'actions' => [
                    'cancel_url' => $this->getCancelUrl($data['order_id']),
                    'return_url' => $this->getReturnUrl($data['order_id']),
                    'callback_url' => $this->getCallbackUrl(),
                ]
            ];

            // Ajouter les informations client si disponibles
            if (isset($data['customer_email']) || isset($data['customer_phone'])) {
                $payload['invoice']['customer'] = [];
                
                if (isset($data['customer_email'])) {
                    $payload['invoice']['customer']['email'] = $data['customer_email'];
                }
                
                if (isset($data['customer_phone'])) {
                    $payload['invoice']['customer']['phone'] = $data['customer_phone'];
                }
                
                // Générer un nom si pas fourni
                if (!isset($payload['invoice']['customer']['name'])) {
                    $payload['invoice']['customer']['name'] = 'Client SunuBoutique';
                }
            }

            // Ajouter les articles selon le format PayDunya
            if (isset($data['items']) && is_array($data['items'])) {
                $items = [];
                foreach ($data['items'] as $index => $item) {
                    $items['item_' . $index] = [
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => (string) $item['unit_price'],
                        'total_price' => (string) $item['total_price'],
                        'description' => $item['description'] ?? '',
                    ];
                }
                $payload['invoice']['items'] = $items;
            }

            // Utiliser l'endpoint de test ou production selon le mode
            $endpoint = $this->mode === 'test' 
                ? '/sandbox-api/v1/checkout-invoice/create'
                : '/api/v1/checkout-invoice/create';

            Log::info('PayDunya request payload', [
                'endpoint' => $this->baseUrl . $endpoint,
                'payload' => $payload,
                'headers' => [
                    'PAYDUNYA-MASTER-KEY' => substr($this->masterKey, 0, 10) . '...',
                    'PAYDUNYA-PRIVATE-KEY' => substr($this->privateKey, 0, 15) . '...',
                    'PAYDUNYA-TOKEN' => substr($this->token, 0, 10) . '...',
                ]
            ]);

            $response = Http::withHeaders([
                'PAYDUNYA-MASTER-KEY' => $this->masterKey,
                'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
                'PAYDUNYA-TOKEN' => $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . $endpoint, $payload);

            Log::info('PayDunya response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Vérifier le response_code selon la documentation
                if (isset($responseData['response_code']) && $responseData['response_code'] === '00') {
                    Log::info('PayDunya invoice created successfully', [
                        'order_id' => $data['order_id'],
                        'invoice_token' => $responseData['token'] ?? null,
                        'response_code' => $responseData['response_code']
                    ]);

                    return [
                        'success' => true,
                        'data' => $responseData
                    ];
                } else {
                    Log::error('PayDunya API returned error code', [
                        'response_code' => $responseData['response_code'] ?? 'unknown',
                        'response_text' => $responseData['response_text'] ?? 'unknown',
                        'order_id' => $data['order_id']
                    ]);

                    return [
                        'success' => false,
                        'error' => $responseData['response_text'] ?? 'Erreur lors de la création de la facture'
                    ];
                }
            }

            Log::error('PayDunya API HTTP Error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'order_id' => $data['order_id']
            ]);

            return [
                'success' => false,
                'error' => 'Erreur HTTP ' . $response->status() . ' lors de la création de la facture'
            ];

        } catch (Exception $e) {
            Log::error('PayDunya Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $data['order_id'] ?? null
            ]);

            return [
                'success' => false,
                'error' => 'Erreur technique lors de la création du paiement'
            ];
        }
    }

    /**
     * Vérifier le statut d'une facture
     */
    public function checkInvoiceStatus($token)
    {
        try {
            // Utiliser l'endpoint de test ou production selon le mode
            $endpoint = $this->mode === 'test' 
                ? '/sandbox-api/v1/checkout-invoice/confirm/' . $token
                : '/api/v1/checkout-invoice/confirm/' . $token;

            $response = Http::withHeaders([
                'PAYDUNYA-MASTER-KEY' => $this->masterKey,
                'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
                'PAYDUNYA-TOKEN' => $this->token,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Vérifier le response_code selon la documentation
                if (isset($responseData['response_code']) && $responseData['response_code'] === '00') {
                    return [
                        'success' => true,
                        'data' => $responseData
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $responseData['response_text'] ?? 'Facture introuvable'
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Erreur HTTP ' . $response->status()
            ];

        } catch (Exception $e) {
            Log::error('PayDunya Check Status Exception', [
                'token' => $token,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la vérification du statut'
            ];
        }
    }

    /**
     * Valider une notification IPN
     */
    public function validateIPN($data)
    {
        try {
            // Vérifier les champs requis
            $requiredFields = ['invoice_token', 'status'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return [
                        'success' => false,
                        'error' => "Champ requis manquant: {$field}"
                    ];
                }
            }

            // Vérifier le statut de la facture auprès de PayDunya
            $statusCheck = $this->checkInvoiceStatus($data['invoice_token']);
            
            if (!$statusCheck['success']) {
                return [
                    'success' => false,
                    'error' => 'Impossible de vérifier le statut de la facture'
                ];
            }

            $invoiceData = $statusCheck['data'];

            // Vérifier que le statut correspond
            if ($invoiceData['status'] !== $data['status']) {
                Log::warning('PayDunya IPN status mismatch', [
                    'ipn_status' => $data['status'],
                    'api_status' => $invoiceData['status'],
                    'token' => $data['invoice_token']
                ]);
            }

            return [
                'success' => true,
                'data' => $invoiceData
            ];

        } catch (Exception $e) {
            Log::error('PayDunya IPN Validation Exception', [
                'data' => $data,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la validation IPN'
            ];
        }
    }

    /**
     * Obtenir l'URL de retour après paiement réussi
     */
    private function getReturnUrl($orderId)
    {
        $baseUrl = $this->getBaseUrl();
        return $baseUrl . '/payment/success?order_id=' . $orderId . '&provider=paydunya';
    }

    /**
     * Obtenir l'URL d'annulation
     */
    private function getCancelUrl($orderId)
    {
        $baseUrl = $this->getBaseUrl();
        return $baseUrl . '/payment/error?order_id=' . $orderId . '&provider=paydunya';
    }

    /**
     * Obtenir l'URL de callback IPN
     */
    private function getCallbackUrl()
    {
        $baseUrl = $this->getBackendBaseUrl();
        return $baseUrl . '/api/payments/paydunya/ipn';
    }

    /**
     * Obtenir l'URL de base pour le frontend (success/error pages)
     */
    private function getBaseUrl()
    {
        return config('app.frontend_url', 'http://localhost:4200');
    }

    /**
     * Obtenir l'URL de base pour le backend (IPN callbacks)
     */
    private function getBackendBaseUrl()
    {
        if (app()->environment('local') && config('services.paydunya.ngrok_url')) {
            return config('services.paydunya.ngrok_url');
        }
        
        return config('app.url');
    }

    /**
     * Obtenir l'URL de paiement
     */
    public function getPaymentUrl($token, $responseText = null)
    {
        // Si response_text est fourni et contient une URL, l'utiliser
        if ($responseText && filter_var($responseText, FILTER_VALIDATE_URL)) {
            return $responseText;
        }
        
        // Sinon, construire l'URL selon le mode
        if ($this->mode === 'test') {
            return 'https://paydunya.com/sandbox-checkout/invoice/' . $token;
        } else {
            return 'https://paydunya.com/checkout/invoice/' . $token;
        }
    }

    /**
     * Formater le montant pour PayDunya
     */
    public function formatAmount($amount)
    {
        // PayDunya attend le montant en centimes pour certaines devises
        // Pour XOF (Franc CFA), utiliser le montant tel quel
        return (float) $amount;
    }

    /**
     * Obtenir les méthodes de paiement disponibles
     */
    public function getPaymentMethods()
    {
        return [
            'card' => 'Carte bancaire',
            'momo' => 'Mobile Money',
            'bank' => 'Virement bancaire',
        ];
    }

    /**
     * Vérifier si le service est configuré
     */
    public function isConfigured()
    {
        return !empty($this->masterKey) && 
               !empty($this->privateKey) && 
               !empty($this->publicKey);
    }
}
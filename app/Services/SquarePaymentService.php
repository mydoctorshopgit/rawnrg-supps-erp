<?php

namespace App\Services;

use Exception;
use Square\Legacy\Environment as LegacyEnvironment;
use Square\Legacy\SquareClient as LegacySquareClient;
use Square\Legacy\Models\CreatePaymentRequest as LegacyCreatePaymentRequest;
use Square\Legacy\Models\Money as LegacyMoney;

class SquarePaymentService
{
    /** @var LegacySquareClient */
    protected $client;

    public function __construct()
    {
        $env = config('services.square.environment') === 'production'
            ? LegacyEnvironment::PRODUCTION
            : LegacyEnvironment::SANDBOX;

        $this->client = new LegacySquareClient([
            'accessToken' => config('services.square.access_token'),
            'environment' => $env,
        ]);
    }

    /**
     * @param float  $amount     Amount in base currency (e.g., 9.89)
     * @param string $currency   ISO-4217 (GBP, USD, etc.)
     * @param string $nonce      Card nonce from Square Web Payments SDK
     * @param string $orderCode  Your Laravel order code (e.g., 20251113-06324560)
     * @param int    $orderId    Your Laravel order ID (not sent to Square)
     * @return array
     */
    public function charge(
        float $amount,
        string $currency,
        string $nonce,
        string $orderCode,
        int $orderId
    ): array {
        try {
            $amountCents = (int) round($amount * 100);
            $idempotencyKey = uniqid('', true);

            $money = new LegacyMoney();
            $money->setAmount($amountCents);
            $money->setCurrency($currency);

            // Constructor requires sourceId and idempotencyKey
            $request = new LegacyCreatePaymentRequest($nonce, $idempotencyKey);

            $request->setAmountMoney($money);
            $request->setLocationId(config('services.square.location_id'));
            $request->setReferenceId($orderCode);   // ← Your order code
            // DO NOT SET ORDER ID → removes currency conflict
            // $request->setOrderId((string) $orderId);

            // Optional: auto-complete the payment
            $request->setAutocomplete(true);

            $api = $this->client->getPaymentsApi();
            $response = $api->createPayment($request);

            if ($response->isSuccess()) {
                return [
                    'success' => true,
                    'payment' => $response->getResult()->getPayment(),
                    'error'   => null,
                ];
            }

            $errors = $response->getErrors() ?? [];
            $msg = collect($errors)->pluck('detail')->implode(' | ');

            return [
                'success' => false,
                'payment' => null,
                'error'   => $msg ?: 'Square payment failed',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'payment' => null,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
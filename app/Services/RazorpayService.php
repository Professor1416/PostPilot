<?php

namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
    }

    public function createOrder(string $planId, int $userId, string $email): array
    {
        $plan = config("postpilot.plans.{$planId}");

        if (!$plan || $plan['amount_paise'] === 0) {
            throw new \InvalidArgumentException("Invalid plan: {$planId}");
        }

        $order = $this->api->order->create([
            'amount'   => $plan['amount_paise'],
            'currency' => 'INR',
            'receipt'  => "pp_{$userId}_" . time(),
            'notes'    => ['plan' => $planId, 'user_id' => $userId, 'email' => $email],
        ]);

        Log::info('Razorpay order created', ['order_id' => $order->id, 'plan' => $planId]);

        return [
            'order_id'   => $order->id,
            'amount'     => $plan['amount_paise'],
            'currency'   => 'INR',
            'plan'       => $planId,
            'plan_label' => $plan['label'],
        ];
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::warning('Razorpay signature mismatch', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

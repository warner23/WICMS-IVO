<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPaymentStripe implements WIPaymentGatewayInterface
{
    public function createCheckout(array $orderData): array
    {
        return [
            'status' => 'not_implemented',
            'gateway' => 'stripe',
            'message' => 'Stripe checkout creation not wired yet.'
        ];
    }

    public function verifyWebhook(string $payload, array $headers = []): bool
    {
        return false;
    }

    public function parseWebhookEvent(string $payload): array
    {
        $json = json_decode($payload, true);
        return is_array($json) ? $json : [];
    }

    public function refund(string $transactionId, float $amount): bool
    {
        return false;
    }
}
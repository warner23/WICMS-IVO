<?php
declare(strict_types=1);

interface WIPaymentGatewayInterface
{
    public function createCheckout(array $orderData): array;
    public function verifyWebhook(string $payload, array $headers = []): bool;
    public function parseWebhookEvent(string $payload): array;
    public function refund(string $transactionId, float $amount): bool;
}
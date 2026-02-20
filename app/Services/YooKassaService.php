<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationFactory;

class YooKassaService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );
    }

    public function createPayment(Payment $payment, Order $order): string
    {
        $response = $this->client->createPayment([
            'amount' => [
                'value'    => number_format($payment->amount, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type'       => 'redirect',
                'return_url' => route('payments.success', ['order' => $order->id]),
            ],
            'capture'     => true,
            'description' => "Заказ #{$order->id} — {$order->performer_name}",
            'metadata'    => [
                'payment_db_id' => $payment->id,
            ],
        ], uniqid('', true));

        $payment->update(['yookassa_id' => $response->getId()]);

        return $response->getConfirmation()->getConfirmationUrl();
    }

    public function createUpgradePayment(\App\Models\Payment $payment, \App\Models\Order $order, \App\Models\OrderUpgrade $upgrade): string
    {
        $toPlan = \App\Models\Order::plans()[$upgrade->to_plan]['name'];

        $response = $this->client->createPayment([
            'amount' => [
                'value'    => number_format($payment->amount, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type'       => 'redirect',
                'return_url' => route('payments.success', ['order' => $order->id]),
            ],
            'capture'     => true,
            'description' => "Улучшение тарифа заказа #{$order->id} → {$toPlan}",
            'metadata'    => [
                'payment_db_id' => $payment->id,
            ],
        ], uniqid('', true));

        $payment->update(['yookassa_id' => $response->getId()]);

        return $response->getConfirmation()->getConfirmationUrl();
    }

    public function createEditPayment(\App\Models\Payment $payment, \App\Models\Order $order, \App\Models\EditRequest $editRequest): string
    {
        $response = $this->client->createPayment([
            'amount' => [
                'value'    => number_format($payment->amount, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type'       => 'redirect',
                'return_url' => route('payments.success', ['order' => $order->id]),
            ],
            'capture'     => true,
            'description' => "Правка заказа #{$order->id} — {$order->performer_name}",
            'metadata'    => [
                'payment_db_id' => $payment->id,
            ],
        ], uniqid('', true));

        $payment->update(['yookassa_id' => $response->getId()]);

        return $response->getConfirmation()->getConfirmationUrl();
    }

    public function createCertificatePayment(\App\Models\Payment $payment, \App\Models\GiftCertificate $cert): string
    {
        $response = $this->client->createPayment([
            'amount' => [
                'value'    => number_format($payment->amount, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type'       => 'redirect',
                'return_url' => route('payments.success.certificate', ['cert' => $cert->id]),
            ],
            'capture'     => true,
            'description' => "Подарочный сертификат на {$cert->amount_rub} ₽",
            'metadata'    => [
                'payment_db_id' => $payment->id,
            ],
        ], uniqid('', true));

        $payment->update(['yookassa_id' => $response->getId()]);

        return $response->getConfirmation()->getConfirmationUrl();
    }

    public function getPayment(string $yookassaId): \YooKassa\Model\Payment\PaymentInterface
    {
        return $this->client->getPaymentInfo($yookassaId);
    }

    public function parseNotification(array $body): \YooKassa\Model\Notification\AbstractNotification
    {
        $factory = new NotificationFactory();
        return $factory->factory($body);
    }
}

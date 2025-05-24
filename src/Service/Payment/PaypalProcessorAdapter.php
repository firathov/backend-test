<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Exception\PaymentFailedException;
use App\Service\PriceCalculatorService;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

readonly class PaypalProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
        private PaypalPaymentProcessor $paypal,
        private PriceCalculatorService $calculator
    ) {}

    public function pay(Product $product, ?Coupon $coupon, string $vatNumber): string
    {
        $result = $this->calculator->calculate($product, $coupon, $vatNumber);
        $amount = (int) round($result['final_price'] * 100);

        try {
            $this->paypal->pay($amount);
        } catch (\Throwable $e) {
            throw new PaymentFailedException('PayPal payment failed: ' . $e->getMessage());
        }

        return 'paypal_' . uniqid();
    }
}

<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Exception\PaymentFailedException;
use App\Service\PriceCalculatorService;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
        private readonly StripePaymentProcessor $stripe,
        private readonly PriceCalculatorService $calculator
    ) {}

    public function pay(Product $product, ?Coupon $coupon, string $vatNumber): string
    {
        $result = $this->calculator->calculate($product, $coupon, $vatNumber);

        if (!$this->stripe->processPayment($result['final_price'])) {
            throw new PaymentFailedException('Stripe payment failed');
        }

        return 'stripe_' . uniqid();
    }
}

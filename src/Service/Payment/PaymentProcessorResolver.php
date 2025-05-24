<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Enum\PaymentProcessorTypeEnum;
use App\Exception\PaymentFailedException;

readonly class PaymentProcessorResolver
{
    public function __construct(
        private StripeProcessorAdapter $stripe,
        private PaypalProcessorAdapter $paypal
    ) {}

    /**
     * @throws PaymentFailedException
     */
    public function getProcessor(PaymentProcessorTypeEnum $provider): PaymentProcessorInterface
    {
        return match ($provider) {
            PaymentProcessorTypeEnum::PAYPAL => $this->paypal,
            PaymentProcessorTypeEnum::STRIPE => $this->stripe,
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Service\Payment;

use App\Enum\PaymentProcessorTypeEnum;
use App\Service\Payment\PaypalProcessorAdapter;
use App\Service\Payment\StripeProcessorAdapter;
use App\Service\Payment\PaymentProcessorResolver;
use PHPUnit\Framework\TestCase;

class PaymentProcessorResolverTest extends TestCase
{
    public function testResolvesStripeAdapter(): void
    {
        $stripe = $this->createMock(StripeProcessorAdapter::class);
        $paypal = $this->createMock(PaypalProcessorAdapter::class);

        $resolver = new PaymentProcessorResolver($stripe, $paypal);

        $result = $resolver->getProcessor(PaymentProcessorTypeEnum::STRIPE);

        $this->assertSame($stripe, $result);
    }

    public function testResolvesPaypalAdapter(): void
    {
        $stripe = $this->createMock(StripeProcessorAdapter::class);
        $paypal = $this->createMock(PaypalProcessorAdapter::class);

        $resolver = new PaymentProcessorResolver($stripe, $paypal);

        $result = $resolver->getProcessor(PaymentProcessorTypeEnum::PAYPAL);

        $this->assertSame($paypal, $result);
    }
}

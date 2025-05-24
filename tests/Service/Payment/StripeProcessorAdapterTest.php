<?php

declare(strict_types=1);

namespace App\Tests\Service\Payment;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponTypeEnum;
use App\Exception\PaymentFailedException;
use App\Service\Payment\StripeProcessorAdapter;
use App\Service\PriceCalculatorService;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeProcessorAdapterTest extends TestCase
{
    public function testSuccessfulPayment(): void
    {
        $product = new Product();
        $product->setName('Iphone');
        $product->setPrice(150.0);

        $coupon = new Coupon();
        $coupon->setCode('DISCOUNT');
        $coupon->setType(CouponTypeEnum::FIXED);
        $coupon->setValue(10.0);

        $calculator = new PriceCalculatorService();

        $stripeMock = $this->createMock(StripePaymentProcessor::class);
        $stripeMock->expects($this->once())
            ->method('processPayment')
            ->with($this->greaterThan(0))
            ->willReturn(true);

        $adapter = new StripeProcessorAdapter($stripeMock, $calculator);
        $purchaseId = $adapter->pay($product, $coupon, 'DE123456789');

        $this->assertStringStartsWith('stripe_', $purchaseId);
    }

    public function testFailedPaymentThrowsException(): void
    {
        $product = new Product();
        $product->setName('Чехол');
        $product->setPrice(50.0);

        $calculator = new PriceCalculatorService();

        $stripeMock = $this->createMock(StripePaymentProcessor::class);
        $stripeMock->method('processPayment')->willReturn(false);

        $adapter = new StripeProcessorAdapter($stripeMock, $calculator);

        $this->expectException(PaymentFailedException::class);
        $adapter->pay($product, null, 'FR123456789');
    }
}

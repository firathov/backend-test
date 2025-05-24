<?php

declare(strict_types=1);

namespace App\Tests\Service\Payment;

use App\Entity\Product;
use App\Entity\Coupon;
use App\Enum\CouponTypeEnum;
use App\Exception\PaymentFailedException;
use App\Service\Payment\PaypalProcessorAdapter;
use App\Service\PriceCalculatorService;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalProcessorAdapterTest extends TestCase
{
    public function testSuccessfulPaypalPayment(): void
    {
        $product = new Product();
        $product->setName('Наушники');
        $product->setPrice(50.0);

        $coupon = new Coupon();
        $coupon->setCode('FIXED5');
        $coupon->setType(CouponTypeEnum::FIXED);
        $coupon->setValue(5.0);

        $calculator = new PriceCalculatorService();

        $paypalMock = $this->createMock(PaypalPaymentProcessor::class);
        $paypalMock->expects($this->once())
            ->method('pay')
            ->with($this->greaterThan(0));

        $adapter = new PaypalProcessorAdapter($paypalMock, $calculator);
        $purchaseId = $adapter->pay($product, $coupon, 'ES123456789');

        $this->assertStringStartsWith('paypal_', $purchaseId);
    }

    public function testPaypalPaymentFailsWithException(): void
    {
        $product = new Product();
        $product->setName('Часы');
        $product->setPrice(9999.99);

        $calculator = new PriceCalculatorService();

        $paypalMock = $this->createMock(PaypalPaymentProcessor::class);
        $paypalMock->method('pay')->willThrowException(new \Exception('Too high price'));

        $adapter = new PaypalProcessorAdapter($paypalMock, $calculator);

        $this->expectException(PaymentFailedException::class);
        $this->expectExceptionMessage('PayPal payment failed: Too high price');

        $adapter->pay($product, null, 'FR123456789');
    }
}

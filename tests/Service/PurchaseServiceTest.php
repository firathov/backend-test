<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Dto\PurchaseRequestDto;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponTypeEnum;
use App\Enum\PaymentProcessorTypeEnum;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Service\Payment\PaymentProcessorInterface;
use App\Service\Payment\PaymentProcessorResolver;
use App\Service\PurchaseService;
use PHPUnit\Framework\TestCase;

class PurchaseServiceTest extends TestCase
{
    public function testSuccessfulPurchase(): void
    {
        $dto = new PurchaseRequestDto();
        $dto->product = 1;
        $dto->taxNumber = 'FR123456789';
        $dto->couponCode = 'WELCOME';
        $dto->paymentProcessor = PaymentProcessorTypeEnum::STRIPE;

        $product = new Product();
        $product->setName('Чехол');
        $product->setPrice(10.0);

        $coupon = new Coupon();
        $coupon->setCode('WELCOME');
        $coupon->setType(CouponTypeEnum::PERCENT);
        $coupon->setValue(10);

        $productRepo = $this->createMock(ProductRepository::class);
        $productRepo->method('find')->with(1)->willReturn($product);

        $couponRepo = $this->createMock(CouponRepository::class);
        $couponRepo->method('findOneBy')->with(['code' => 'WELCOME'])->willReturn($coupon);

        $processorMock = $this->createMock(PaymentProcessorInterface::class);
        $processorMock->expects($this->once())->method('pay')->willReturn('test_purchase_123');

        $resolver = $this->createMock(PaymentProcessorResolver::class);
        $resolver->method('getProcessor')->willReturn($processorMock);

        $service = new PurchaseService($productRepo, $couponRepo, $resolver);
        $result = $service->purchase($dto);

        $this->assertEquals('test_purchase_123', $result);
    }
}

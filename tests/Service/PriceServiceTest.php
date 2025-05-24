<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Dto\PriceRequestDto;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponTypeEnum;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Service\PriceCalculatorService;
use App\Service\PriceService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PriceServiceTest extends TestCase
{
    public function testCalculatePriceSuccessfully(): void
    {
        $dto = new PriceRequestDto();
        $dto->product = 1;
        $dto->taxNumber = 'DE123456789';
        $dto->couponCode = 'SUMMER10';

        $product = new Product();
        $product->setName('Iphone');
        $product->setPrice(100.0);

        $coupon = new Coupon();
        $coupon->setCode('SUMMER10');
        $coupon->setType(CouponTypeEnum::PERCENT);
        $coupon->setValue(10.0);

        $productRepo = $this->createMock(ProductRepository::class);
        $productRepo->method('find')->with(1)->willReturn($product);

        $couponRepo = $this->createMock(CouponRepository::class);
        $couponRepo->method('findOneBy')->with(['code' => 'SUMMER10'])->willReturn($coupon);

        $calculator = new PriceCalculatorService();

        $service = new PriceService($productRepo, $couponRepo, $calculator);
        $result = $service->calculate($dto);

        $this->assertArrayHasKey('product', $result);
        $this->assertArrayHasKey('final_price', $result);
        $this->assertEquals('Iphone', $result['product']);
    }

    public function testThrowsWhenProductNotFound(): void
    {
        $dto = new PriceRequestDto();
        $dto->product = 99;
        $dto->taxNumber = 'DE123456789';

        $productRepo = $this->createMock(ProductRepository::class);
        $productRepo->method('find')->with(99)->willReturn(null);

        $couponRepo = $this->createMock(CouponRepository::class);
        $calculator = new PriceCalculatorService();

        $service = new PriceService($productRepo, $couponRepo, $calculator);

        $this->expectException(NotFoundHttpException::class);
        $service->calculate($dto);
    }

    public function testThrowsWhenCouponInvalid(): void
    {
        $dto = new PriceRequestDto();
        $dto->product = 1;
        $dto->taxNumber = 'DE123456789';
        $dto->couponCode = 'INVALID';

        $product = new Product();
        $product->setName('Iphone');
        $product->setPrice(100.0);

        $productRepo = $this->createMock(ProductRepository::class);
        $productRepo->method('find')->with(1)->willReturn($product);

        $couponRepo = $this->createMock(CouponRepository::class);
        $couponRepo->method('findOneBy')->with(['code' => 'INVALID'])->willReturn(null);

        $calculator = new PriceCalculatorService();

        $service = new PriceService($productRepo, $couponRepo, $calculator);

        $this->expectException(\InvalidArgumentException::class);
        $service->calculate($dto);
    }
}

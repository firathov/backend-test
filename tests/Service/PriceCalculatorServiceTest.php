<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponTypeEnum;
use App\Service\PriceCalculatorService;
use PHPUnit\Framework\TestCase;

class PriceCalculatorServiceTest extends TestCase
{
    public function testCalculateWithPercentCoupon(): void
    {
        $product = new Product();
        $product->setName('Iphone');
        $product->setPrice(100.0);

        $coupon = new Coupon();
        $coupon->setCode('SUMMER10');
        $coupon->setType(CouponTypeEnum::PERCENT);
        $coupon->setValue(10.0);

        $service = new PriceCalculatorService();
        $result = $service->calculate($product, $coupon, 'DE123456789');

        $this->assertEquals(100.0, $result['base_price']);
        $this->assertEquals(10.0, $result['discount']);
        $this->assertEquals(107.1, $result['final_price']);
    }

    public function testCalculateWithFixedCoupon(): void
    {
        $product = new Product();
        $product->setName('Чехол');
        $product->setPrice(10.0);

        $coupon = new Coupon();
        $coupon->setCode('WELCOME5');
        $coupon->setType(CouponTypeEnum::FIXED);
        $coupon->setValue(5.0);

        $service = new PriceCalculatorService();
        $result = $service->calculate($product, $coupon, 'FR123456789');

        $this->assertEquals(10.0, $result['base_price']);
        $this->assertEquals(5.0, $result['discount']);
        $this->assertEquals(6.0, $result['final_price']);
    }
}

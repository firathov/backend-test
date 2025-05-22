<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponTypeEnum;

class PriceCalculatorService
{
    public function calculate(Product $product, ?Coupon $coupon, string $vatNumber): array
    {
        $basePrice = $product->getPrice();
        $discount = 0.0;

        if ($coupon) {
            if ($coupon->getType() === CouponTypeEnum::PERCENT) {
                $discount = $basePrice * ($coupon->getValue() / 100);
            } elseif ($coupon->getType() === CouponTypeEnum::FIXED) {
                $discount = $coupon->getValue();
            }
        }

        $countryCode = substr($vatNumber, 0, 2);
        $taxRate = $this->getTaxRateByCountry($countryCode);

        $priceAfterDiscount = max(0, $basePrice - $discount);
        $taxAmount = $priceAfterDiscount * $taxRate;
        $finalPrice = $priceAfterDiscount + $taxAmount;

        return [
            'product' => $product->getName(),
            'base_price' => $basePrice,
            'discount' => round($discount, 2),
            'tax' => round($taxAmount, 2),
            'final_price' => round($finalPrice, 2),
        ];
    }

    private function getTaxRateByCountry(string $countryCode): float
    {
        return match (strtoupper($countryCode)) {
            'DE' => 0.19,
            'FR' => 0.20,
            'IT' => 0.22,
            'ES' => 0.21,
            default => 0.0,
        };
    }
}

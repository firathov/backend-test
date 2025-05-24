<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\PriceRequestDto;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PriceService
{
    public function __construct(
        private readonly ProductRepository $productRepo,
        private readonly CouponRepository  $couponRepo,
        private readonly PriceCalculatorService $calculator
    ) {}

    public function calculate(PriceRequestDto $dto): array
    {
        $product = $this->productRepo->find($dto->product);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }

        $coupon = null;
        if ($dto->couponCode) {
            $coupon = $this->couponRepo->findOneBy(['code' => $dto->couponCode]);
            if (!$coupon) {
                throw new \InvalidArgumentException('Invalid coupon code');
            }
        }

        return $this->calculator->calculate($product, $coupon, $dto->taxNumber);
    }
}

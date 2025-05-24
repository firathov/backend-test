<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\PurchaseRequestDto;
use App\Exception\PaymentFailedException;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Service\Payment\PaymentProcessorResolver;

class PurchaseService
{
    public function __construct(
        private readonly ProductRepository $productRepo,
        private readonly CouponRepository  $couponRepo,
        private readonly PaymentProcessorResolver $resolver
    ) {}

    /**
     * @throws \InvalidArgumentException|PaymentFailedException
     */
    public function purchase(PurchaseRequestDto $dto): string
    {
        $product = $this->productRepo->find($dto->product);
        if (!$product) {
            throw new \InvalidArgumentException('Product not found');
        }

        $coupon = null;
        if ($dto->couponCode) {
            $coupon = $this->couponRepo->findOneBy(['code' => $dto->couponCode]);
            if (!$coupon) {
                throw new \InvalidArgumentException('Invalid coupon code');
            }
        }

        $processor = $this->resolver->getProcessor($dto->paymentProcessor);
        return $processor->pay($product, $coupon, $dto->taxNumber);
    }
}

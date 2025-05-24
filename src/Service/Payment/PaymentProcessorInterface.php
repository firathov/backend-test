<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Exception\PaymentFailedException;

interface PaymentProcessorInterface
{
    /**
     * @throws PaymentFailedException
     */
    public function pay(Product $product, ?Coupon $coupon, string $vatNumber): string;
}

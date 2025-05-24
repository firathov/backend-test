<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\PaymentProcessorTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $product;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}[A-Z0-9]+$/',
        message: 'Invalid tax number format'
    )]
    public string $taxNumber;

    #[Assert\Type('string')]
    public ?string $couponCode = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: PaymentProcessorTypeEnum::class)]
    public PaymentProcessorTypeEnum $paymentProcessor;
}

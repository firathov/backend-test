<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PriceRequestDto
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
}

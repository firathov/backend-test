<?php

declare(strict_types=1);

namespace App\Enum;

enum CouponTypeEnum: string
{
    case FIXED = 'fixed';
    case PERCENT = 'percent';
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PriceRequestDto;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Service\PriceCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculatePrice(
        #[MapRequestPayload] PriceRequestDto $dto,
        ProductRepository $productRepo,
        CouponRepository $couponRepo,
        PriceCalculatorService $priceCalculatorService
    ): JsonResponse {
        $product = $productRepo->find($dto->product);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $coupon = null;
        if ($dto->couponCode) {
            $coupon = $couponRepo->findOneBy(['code' => $dto->couponCode]);
            if (!$coupon) {
                return $this->json(['error' => 'Invalid coupon code'], 400);
            }
        }

        $result = $priceCalculatorService->calculate($product, $coupon, $dto->taxNumber);
        return $this->json($result);
    }
}

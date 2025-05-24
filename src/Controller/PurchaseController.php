<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PriceRequestDto;
use App\Dto\PurchaseRequestDto;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Service\Payment\PaymentProcessorResolver;
use App\Exception\PaymentFailedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(
        #[MapRequestPayload] PurchaseRequestDto $dto,
        ProductRepository $productRepo,
        CouponRepository $couponRepo,
        PaymentProcessorResolver $resolver
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

        try {
            $processor = $resolver->getProcessor($dto->paymentProcessor);
            $purchaseId = $processor->pay($product, $coupon, $dto->taxNumber);
        } catch (PaymentFailedException $e) {
            return $this->json(['error' => $e->getMessage()], 402);
        }

        return $this->json(['purchase_id' => $purchaseId]);
    }
}

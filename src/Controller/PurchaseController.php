<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PurchaseRequestDto;
use App\Exception\PaymentFailedException;
use App\Service\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(
        #[MapRequestPayload] PurchaseRequestDto $dto,
        PurchaseService $purchaseService
    ): JsonResponse {
        try {
            $purchaseId = $purchaseService->purchase($dto);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (PaymentFailedException $e) {
            return $this->json(['error' => $e->getMessage()], 402);
        }

        return $this->json(['purchase_id' => $purchaseId]);
    }
}

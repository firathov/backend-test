<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PriceRequestDto;
use App\Service\PriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculatePrice(
        #[MapRequestPayload] PriceRequestDto $dto,
        PriceService $priceService
    ): JsonResponse {
        try {
            $result = $priceService->calculate($dto);
        } catch (NotFoundHttpException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        return $this->json($result);
    }
}

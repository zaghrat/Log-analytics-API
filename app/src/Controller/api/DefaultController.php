<?php

namespace App\Controller\api;

use App\Repository\LogRepository;
use App\Service\ValidationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{

    #[Route('/count', name: 'searchLogs', methods: ["GET"])]
    public function count(
        Request $request,
        ValidationService $validationService,
        LogRepository $logRepository
    ): JsonResponse
    {
        $errors = $validationService->validateQueryParameters($request);

        if (count($errors)) {
            return $this->json(
                ['errors'    => $errors],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $serviceNames = $request->get('serviceNames', []);
        $statusCode = $request->query->get('statusCode');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        $count = $logRepository->countOfLogsLine($serviceNames, $statusCode, $startDate, $endDate);

        return $this->json([
            'CountItem' => [
                'counter' =>   $count,
            ]
        ]);
    }
}

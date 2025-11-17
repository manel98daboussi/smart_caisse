<?php

namespace App\Controller;

use App\Service\RapportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ventes','api_ventes_')]
class DashboardController extends AbstractController
{
    private RapportService $rapportService;

    public function __construct(RapportService $rapportService)
    {
        $this->rapportService = $rapportService;
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        $stats = $this->rapportService->getDashboardStats();
        return $this->json($stats ??[]);
    }

    #[Route('/daily-report/{dateVente}', name: 'daily_report', methods: ['GET'])]
    public function getDailyReport(string $dateVente): JsonResponse
    {
        $reportDate = new \DateTime($dateVente);
        $report = $this->rapportService->getDailyReport($reportDate);
        
        return $this->json($report);
    }

    #[Route('/history', name: 'history', methods: ['GET'])]
    public function getSalesHistory(): JsonResponse
    {
        // Get page from query parameters, default to 1
        $page = intval($_GET['page'] ?? 1);
        $history = $this->rapportService->getSalesHistory($page);
        
        return $this->json($history);
    }

    
}
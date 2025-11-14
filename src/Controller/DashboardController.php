<?php

namespace App\Controller;

use App\Service\RapportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/dashboard')]
class DashboardController extends AbstractController
{
    private RapportService $rapportService;

    public function __construct(RapportService $rapportService)
    {
        $this->rapportService = $rapportService;
    }

    #[Route('/stats', name: 'dashboard_stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        $stats = $this->rapportService->getDashboardStats();
        
        return $this->json($stats);
    }

    #[Route('/daily-report/{date}', name: 'daily_report', methods: ['GET'])]
    public function getDailyReport(string $date): JsonResponse
    {
        $reportDate = new \DateTime($date);
        $report = $this->rapportService->getDailyReport($reportDate);
        
        return $this->json($report);
    }

    #[Route('/sales-history', name: 'sales_history', methods: ['GET'])]
    public function getSalesHistory(): JsonResponse
    {
        // Get page from query parameters, default to 1
        $page = intval($_GET['page'] ?? 1);
        $history = $this->rapportService->getSalesHistory($page);
        
        return $this->json($history);
    }

    #[Route('/best-selling-products', name: 'best_selling_products', methods: ['GET'])]
    public function getBestSellingProducts(): JsonResponse
    {
        $products = $this->rapportService->getBestSellingProducts();
        
        return $this->json($products);
    }
}
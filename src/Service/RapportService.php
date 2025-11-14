<?php

namespace App\Service;

use App\Entity\Vente;
use App\Entity\LigneDeVente;
use App\Repository\VenteRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;

class RapportService
{
    private EntityManagerInterface $entityManager;
    private VenteRepository $venteRepository;
    private ProduitRepository $produitRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        VenteRepository $venteRepository,
        ProduitRepository $produitRepository
    ) {
        $this->entityManager = $entityManager;
        $this->venteRepository = $venteRepository;
        $this->produitRepository = $produitRepository;
    }

    /**
     * Get daily sales report
     */
    public function getDailyReport(\DateTimeInterface $date): array
    {
        $startOfDay = (new \DateTimeImmutable($date->format('Y-m-d H:i:s')))->setTime(0, 0, 0);
        $endOfDay = (new \DateTimeImmutable($date->format('Y-m-d H:i:s')))->setTime(23, 59, 59);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte('dateVente', $startOfDay))
            ->andWhere(Criteria::expr()->lte('dateVente', $endOfDay));

        $ventes = $this->venteRepository->matching($criteria);

        $totalVentes = 0;
        $totalRecettes = 0;
        $produitsVendus = [];

        foreach ($ventes as $vente) {
            $totalVentes++;
            $totalRecettes += $vente->getTotalTTC();

            foreach ($vente->getLignes() as $ligne) {
                $produitId = $ligne->getProduit()->getId();
                if (!isset($produitsVendus[$produitId])) {
                    $produitsVendus[$produitId] = [
                        'produit' => $ligne->getProduit(),
                        'quantite' => 0,
                        'total' => 0
                    ];
                }
                
                $produitsVendus[$produitId]['quantite'] += $ligne->getQuantite();
                $produitsVendus[$produitId]['total'] += $ligne->getSousTotal();
            }
        }

        return [
            'date' => $date,
            'nombreVentes' => $totalVentes,
            'recettes' => $totalRecettes,
            'produitsVendus' => array_values($produitsVendus)
        ];
    }

    /**
     * Get sales history with pagination
     */
    public function getSalesHistory(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
        
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('v')
           ->from(Vente::class, 'v')
           ->orderBy('v.dateVente', 'DESC')
           ->setFirstResult($offset)
           ->setMaxResults($limit);
        
        $ventes = $qb->getQuery()->getResult();
        
        $qbCount = $this->entityManager->createQueryBuilder();
        $qbCount->select('COUNT(v.id)')
                ->from(Vente::class, 'v');
        
        $total = $qbCount->getQuery()->getSingleScalarResult();
        
        return [
            'ventes' => $ventes,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ];
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts(int $limit = 10): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p.nom as produit_nom, SUM(l.quantite) as quantite_totale')
           ->from(LigneDeVente::class, 'l')
           ->join('l.produit', 'p')
           ->groupBy('p.id')
           ->orderBy('quantite_totale', 'DESC')
           ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Generate dashboard statistics
     */
    public function getDashboardStats(): array
    {
        // Today's stats
        $today = new \DateTime();
        $todayReport = $this->getDailyReport($today);
        
        // This week's stats
        $startOfWeek = (new \DateTime())->modify('monday this week');
        $endOfWeek = (new \DateTime())->modify('sunday this week');
        
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(v.id) as nombre_ventes, SUM(v.totalTTC) as recette')
           ->from(Vente::class, 'v')
           ->where('v.dateVente >= :startOfWeek')
           ->andWhere('v.dateVente <= :endOfWeek')
           ->setParameter('startOfWeek', $startOfWeek)
           ->setParameter('endOfWeek', $endOfWeek);
        
        $weeklyStats = $qb->getQuery()->getSingleResult();
        
        // This month's stats
        $startOfMonth = (new \DateTime())->modify('first day of this month');
        $endOfMonth = (new \DateTime())->modify('last day of this month');
        
        $qb2 = $this->entityManager->createQueryBuilder();
        $qb2->select('COUNT(v.id) as nombre_ventes, SUM(v.totalTTC) as recette')
            ->from(Vente::class, 'v')
            ->where('v.dateVente >= :startOfMonth')
            ->andWhere('v.dateVente <= :endOfMonth')
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('endOfMonth', $endOfMonth);
        
        $monthlyStats = $qb2->getQuery()->getSingleResult();
        
        return [
            'today' => $todayReport,
            'this_week' => [
                'nombre_ventes' => $weeklyStats['nombre_ventes'] ?? 0,
                'recette' => $weeklyStats['recette'] ?? 0
            ],
            'this_month' => [
                'nombre_ventes' => $monthlyStats['nombre_ventes'] ?? 0,
                'recette' => $monthlyStats['recette'] ?? 0
            ],
            'best_selling_products' => $this->getBestSellingProducts(5)
        ];
    }
}
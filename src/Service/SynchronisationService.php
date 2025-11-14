<?php

namespace App\Service;

use App\Entity\Vente;
use App\Entity\LigneDeVente;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SynchronisationService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Process offline sales stored locally and sync with main database
     */
    public function synchronizeOfflineSales(array $offlineSalesData): array
    {
        $syncedSales = [];
        $failedSales = [];
        
        foreach ($offlineSalesData as $saleData) {
            try {
                $vente = new Vente();
                
                // Set sale properties
                $vente->setDateVente(new \DateTime($saleData['dateVente']));
                $vente->setTotalHT($saleData['totalHT']);
                $vente->setTotalTTC($saleData['totalTTC']);
                $vente->setModePaiement($saleData['modePaiement'] ?? 'cash');
                
                // Handle user if present
                if (isset($saleData['userId'])) {
                    $user = $this->entityManager->getRepository(\App\Entity\User::class)->find($saleData['userId']);
                    if ($user) {
                        $vente->setUser($user);
                    }
                }
                
                // Handle session if present
                if (isset($saleData['sessionId'])) {
                    $session = $this->entityManager->getRepository(\App\Entity\Session::class)->find($saleData['sessionId']);
                    if ($session) {
                        $vente->setSession($session);
                    }
                }
                
                // Create line items
                foreach ($saleData['lignes'] as $ligneData) {
                    $ligne = new LigneDeVente();
                    $ligne->setQuantite($ligneData['quantite']);
                    $ligne->setPrixUnitaire($ligneData['prixUnitaire']);
                    
                    // Find product
                    $produit = $this->entityManager->getRepository(\App\Entity\Produit::class)->find($ligneData['produitId']);
                    if ($produit) {
                        $ligne->setProduit($produit);
                        
                        // Update stock
                        $produit->decrementStock($ligneData['quantite']);
                    }
                    
                    $vente->addLigne($ligne);
                }
                
                // Persist the sale
                $this->entityManager->persist($vente);
                $syncedSales[] = $saleData['id'];
            } catch (\Exception $e) {
                $this->logger->error('Failed to sync sale: ' . $e->getMessage(), [
                    'sale_data' => $saleData
                ]);
                $failedSales[] = [
                    'id' => $saleData['id'],
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // Flush all changes
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error('Failed to flush synchronized sales: ' . $e->getMessage());
            return [
                'synced' => [],
                'failed' => array_merge($failedSales, $syncedSales) // Move all to failed if flush fails
            ];
        }
        
        return [
            'synced' => $syncedSales,
            'failed' => $failedSales
        ];
    }

    /**
     * Export sales data for offline storage
     */
    public function exportSalesData(\DateTime $fromDate): array
    {
        $repo = $this->entityManager->getRepository(Vente::class);
        $qb = $repo->createQueryBuilder('v')
            ->where('v.dateVente >= :fromDate')
            ->setParameter('fromDate', $fromDate)
            ->orderBy('v.dateVente', 'ASC');

        $ventes = $qb->getQuery()->getResult();
        
        $exportData = [];
        foreach ($ventes as $vente) {
            $lignesData = [];
            foreach ($vente->getLignes() as $ligne) {
                $lignesData[] = [
                    'quantite' => $ligne->getQuantite(),
                    'prixUnitaire' => $ligne->getPrixUnitaire(),
                    'produitId' => $ligne->getProduit()->getId(),
                    'produitNom' => $ligne->getProduit()->getNom()
                ];
            }
            
            $exportData[] = [
                'id' => $vente->getId(),
                'dateVente' => $vente->getDateVente()->format(\DateTime::ISO8601),
                'totalHT' => $vente->getTotalHT(),
                'totalTTC' => $vente->getTotalTTC(),
                'modePaiement' => $vente->getModePaiement(),
                'userId' => $vente->getUser() ? $vente->getUser()->getId() : null,
                'sessionId' => $vente->getSession() ? $vente->getSession()->getId() : null,
                'lignes' => $lignesData
            ];
        }
        
        return $exportData;
    }
}
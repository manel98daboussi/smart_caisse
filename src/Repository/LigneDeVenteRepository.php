<?php

namespace App\Repository;

use App\Entity\LigneDeVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LigneDeVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneDeVente::class);
    }

    public function findByVente(int $venteId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.vente = :vente')
            ->setParameter('vente', $venteId)
            ->getQuery()
            ->getResult();
    }
}

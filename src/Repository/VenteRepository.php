<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    public function findBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.session = :session')
            ->setParameter('session', $sessionId)
            ->orderBy('v.dateVente', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalVentesJour(\DateTimeInterface $date): float
    {
        return (float) $this->createQueryBuilder('v')
            ->select('SUM(v.totalTTC)')
            ->andWhere('DATE(v.dateVente) = :jour')
            ->setParameter('jour', $date->format('Y-m-d'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}

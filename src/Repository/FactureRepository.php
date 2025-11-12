<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function findByNumero(string $numero): ?Facture
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.numero = :num')
            ->setParameter('num', $numero)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

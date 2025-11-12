<?php

namespace App\Repository;

use App\Entity\TableQR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TableQRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableQR::class);
    }

    public function findByQrCode(string $qrCode): ?TableQR
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.qrCode = :code')
            ->setParameter('code', $qrCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

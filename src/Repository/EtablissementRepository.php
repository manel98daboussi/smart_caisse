<?php

namespace App\Repository;

use App\Entity\Etablissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EtablissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etablissement::class);
    }

    public function findByNom(string $nom): ?Etablissement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('LOWER(e.nom) = :nom')
            ->setParameter('nom', strtolower($nom))
            ->getQuery()
            ->getOneOrNullResult();
    }
}

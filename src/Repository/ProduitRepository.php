<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.actif = true')
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByNom(string $nom): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('LOWER(p.nom) LIKE :nom')
            ->setParameter('nom', '%' . strtolower($nom) . '%')
            ->getQuery()
            ->getResult();
    }
}

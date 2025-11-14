<?php

namespace App\Repository;

use App\Entity\Configuration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Configuration>
 */
class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuration::class);
    }

    public function findOneOrCreate(): Configuration
    {
        $config = $this->findOneBy([]);
        if (!$config) {
            $config = new Configuration();
            // Set default values
            $config->setNomEntreprise('Smart Caisse');
            $config->setDevise('EUR');
            $config->setTauxTva(20.0);
            $config->setFrequenceSync(30);
            
            $entityManager = $this->getEntityManager();
            $entityManager->persist($config);
            $entityManager->flush();
        }
        
        return $config;
    }
}
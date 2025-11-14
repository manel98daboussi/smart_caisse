<?php

namespace App\Command;

use App\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitializeConfigCommand extends Command
{
    protected static $defaultName = 'app:init-config';
    protected static $defaultDescription = 'Initialize application configuration with default values';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check if configuration already exists
        $existingConfig = $this->entityManager->getRepository(Configuration::class)->findOneBy([]);
        
        if ($existingConfig) {
            $io->warning('Configuration already exists. Skipping initialization.');
            return Command::SUCCESS;
        }

        // Create new configuration with default values
        $config = new Configuration();
        $config->setNomEntreprise('Smart Caisse');
        $config->setDevise('EUR');
        $config->setTauxTva(20.0);
        $config->setFrequenceSync(30);
        $config->setImpressionActive(false);

        $this->entityManager->persist($config);
        $this->entityManager->flush();

        $io->success('Configuration initialized successfully!');

        return Command::SUCCESS;
    }
}
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251113161612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, logo_url VARCHAR(255) DEFAULT NULL, taux_tva DOUBLE PRECISION NOT NULL, devise VARCHAR(10) NOT NULL, symbole_devise VARCHAR(50) DEFAULT NULL, impression_active TINYINT(1) NOT NULL, type_imprimante VARCHAR(50) DEFAULT NULL, adresse_imprimante VARCHAR(255) DEFAULT NULL, frequence_sync INT NOT NULL, url_serveur_sync VARCHAR(255) DEFAULT NULL, api_key VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD remise DOUBLE PRECISION NOT NULL, ADD date_debut_remise DATETIME DEFAULT NULL, ADD date_fin_remise DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE configuration');
        $this->addSql('ALTER TABLE produit DROP remise, DROP date_debut_remise, DROP date_fin_remise');
    }
}

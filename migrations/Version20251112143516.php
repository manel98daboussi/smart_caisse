<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112143516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_table (id INT AUTO_INCREMENT NOT NULL, table_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date_commande DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, INDEX IDX_CF415085ECFF285C (table_id), INDEX IDX_CF415085A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etablissement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, adresse VARCHAR(255) NOT NULL, email VARCHAR(150) DEFAULT NULL, telephone VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, vente_id INT DEFAULT NULL, numero VARCHAR(50) NOT NULL, date_emission DATETIME NOT NULL, montant_total DOUBLE PRECISION NOT NULL, email_client VARCHAR(150) DEFAULT NULL, UNIQUE INDEX UNIQ_FE8664107DC7170A (vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne_de_vente (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, vente_id INT DEFAULT NULL, quantite INT NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, INDEX IDX_4E7642FF347EFB (produit_id), INDEX IDX_4E7642F7DC7170A (vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, prix DOUBLE PRECISION NOT NULL, stock INT NOT NULL, categorie VARCHAR(100) DEFAULT NULL, tva DOUBLE PRECISION NOT NULL, actif TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date_ouverture DATETIME NOT NULL, date_fermeture DATETIME DEFAULT NULL, montant_initial DOUBLE PRECISION NOT NULL, montant_final DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D044D5D4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE table_qr (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, numero VARCHAR(50) NOT NULL, qr_code VARCHAR(255) DEFAULT NULL, INDEX IDX_9A300554FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, actif TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date_vente DATETIME NOT NULL, total_ht DOUBLE PRECISION NOT NULL, total_ttc DOUBLE PRECISION NOT NULL, mode_paiement VARCHAR(50) NOT NULL, INDEX IDX_888A2A4C613FECDF (session_id), INDEX IDX_888A2A4CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_table ADD CONSTRAINT FK_CF415085ECFF285C FOREIGN KEY (table_id) REFERENCES table_qr (id)');
        $this->addSql('ALTER TABLE commande_table ADD CONSTRAINT FK_CF415085A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664107DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('ALTER TABLE ligne_de_vente ADD CONSTRAINT FK_4E7642FF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ligne_de_vente ADD CONSTRAINT FK_4E7642F7DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE table_qr ADD CONSTRAINT FK_9A300554FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_table DROP FOREIGN KEY FK_CF415085ECFF285C');
        $this->addSql('ALTER TABLE commande_table DROP FOREIGN KEY FK_CF415085A76ED395');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664107DC7170A');
        $this->addSql('ALTER TABLE ligne_de_vente DROP FOREIGN KEY FK_4E7642FF347EFB');
        $this->addSql('ALTER TABLE ligne_de_vente DROP FOREIGN KEY FK_4E7642F7DC7170A');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4A76ED395');
        $this->addSql('ALTER TABLE table_qr DROP FOREIGN KEY FK_9A300554FF631228');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C613FECDF');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CA76ED395');
        $this->addSql('DROP TABLE commande_table');
        $this->addSql('DROP TABLE etablissement');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE ligne_de_vente');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE table_qr');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vente');
    }
}

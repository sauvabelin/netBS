<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190208080122 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE creance DROP FOREIGN KEY FK_82D1060E7F2DEE08');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E7F2DEE08');
        $this->addSql('ALTER TABLE rappel DROP FOREIGN KEY FK_303A29C97F2DEE08');
        $this->addSql('DROP TABLE creance');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE rappel');
        $this->addSql('ALTER TABLE ovesco_facturation_paiements ADD transactionDetails LONGTEXT DEFAULT NULL');
        // $this->addSql('CREATE UNIQUE INDEX UNIQ_2BBC099EFAD56E62 ON ovesco_facturation_comptes (iban)');
        $this->addSql('ALTER TABLE ovesco_facturation_factures ADD old_fichier_id INT NOT NULL');
        $this->addSql('CREATE TABLE ovesco_facturation_facture_models (id INT AUTO_INCREMENT NOT NULL, name TINYTEXT NOT NULL, application_rule TINYTEXT DEFAULT NULL COLLATE utf8_unicode_ci, top_description LONGTEXT NOT NULL, titre LONGTEXT NOT NULL, bottom_salutations LONGTEXT NOT NULL, signataire VARCHAR(255) NOT NULL, group_name VARCHAR(255) NOT NULL, rue VARCHAR(255) NOT NULL, npa_ville VARCHAR(255) NOT NULL, city_from VARCHAR(255) NOT NULL, poids INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE creance (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, montant DOUBLE PRECISION NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, remarques LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, debiteur_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_82D1060E7F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, remarques LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, debiteur_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, remarques LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_B1DC7A1E7F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rappel (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, remarques LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_303A29C97F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE creance ADD CONSTRAINT FK_82D1060E7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE rappel ADD CONSTRAINT FK_303A29C97F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('DROP INDEX UNIQ_2BBC099EFAD56E62 ON ovesco_facturation_comptes');
        $this->addSql('ALTER TABLE ovesco_facturation_comptes DROP iban');
        $this->addSql('ALTER TABLE ovesco_facturation_factures DROP old_fichier_id');
    }
}


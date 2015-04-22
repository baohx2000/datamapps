<?php

namespace DocMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150422054829 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE phones (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, phoneNumber VARCHAR(255) NOT NULL, created DATETIME NOT NULL, modified DATETIME NOT NULL, INDEX IDX_E3282EF5217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, created DATETIME NOT NULL, modified DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE addresses (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, created DATETIME NOT NULL, modified DATETIME NOT NULL, INDEX IDX_6FCA7516217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE phones ADD CONSTRAINT FK_E3282EF5217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA7516217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE phones DROP FOREIGN KEY FK_E3282EF5217BBB47');
        $this->addSql('ALTER TABLE addresses DROP FOREIGN KEY FK_6FCA7516217BBB47');
        $this->addSql('DROP TABLE phones');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE addresses');
    }
}

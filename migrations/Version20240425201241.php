<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425201241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medico ADD especialidad_id INT NOT NULL, DROP especialidad');
        $this->addSql('ALTER TABLE medico ADD CONSTRAINT FK_34E5914C16A490EC FOREIGN KEY (especialidad_id) REFERENCES especialidad (id)');
        $this->addSql('CREATE INDEX IDX_34E5914C16A490EC ON medico (especialidad_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medico DROP FOREIGN KEY FK_34E5914C16A490EC');
        $this->addSql('DROP INDEX IDX_34E5914C16A490EC ON medico');
        $this->addSql('ALTER TABLE medico ADD especialidad VARCHAR(255) NOT NULL, DROP especialidad_id');
    }
}

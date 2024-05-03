<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502063331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita ADD especialidad_id INT NOT NULL');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A6216A490EC FOREIGN KEY (especialidad_id) REFERENCES especialidad (id)');
        $this->addSql('CREATE INDEX IDX_3E379A6216A490EC ON cita (especialidad_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A6216A490EC');
        $this->addSql('DROP INDEX IDX_3E379A6216A490EC ON cita');
        $this->addSql('ALTER TABLE cita DROP especialidad_id');
    }
}

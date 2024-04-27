<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240427151054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62870EE412');
        $this->addSql('DROP INDEX IDX_3E379A62870EE412 ON cita');
        $this->addSql('ALTER TABLE cita CHANGE médico_id medico_id INT NOT NULL');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62A7FB1C0C FOREIGN KEY (medico_id) REFERENCES medico (id)');
        $this->addSql('CREATE INDEX IDX_3E379A62A7FB1C0C ON cita (medico_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62A7FB1C0C');
        $this->addSql('DROP INDEX IDX_3E379A62A7FB1C0C ON cita');
        $this->addSql('ALTER TABLE cita CHANGE medico_id médico_id INT NOT NULL');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62870EE412 FOREIGN KEY (médico_id) REFERENCES medico (id)');
        $this->addSql('CREATE INDEX IDX_3E379A62870EE412 ON cita (médico_id)');
    }
}

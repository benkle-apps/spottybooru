<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200809191202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Initial SQL';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
            CREATE TABLE post (
                id UUID NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT DEFAULT NULL,
                safety VARCHAR(255) CHECK(safety IN ('safe', 'sketchy', 'unsafe')) NOT NULL,
                tags jsonb NOT NULL,
                checksum VARCHAR(64) NOT NULL,
                size INT NOT NULL,
                width INT NOT NULL,
                height INT NOT NULL,
                mime VARCHAR(32) NOT NULL,
                created TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                updated TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                file VARCHAR(255) NOT NULL,
                thumbail VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            );
SQL);
        $this->addSql("COMMENT ON COLUMN post.id IS '(DC2Type:uuid)';");
        $this->addSql("COMMENT ON COLUMN post.safety IS '(DC2Type:SafetyEnum)';");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE post;');
    }
}

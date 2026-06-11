<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;

final class Version20260611113700 extends Migration
{
    public function getDescription(): string
    {
        return 'MAIN TABLES FOR WORKSPACE';
    }

    public function up(Schema $schema): void
    {
        $this->addEachSql(file_get_contents(__DIR__ . '/up/' . $this->getVersion() . '.sql'));
    }

    public function down(Schema $schema): void
    {
        $this->addEachSql(file_get_contents(__DIR__ . '/down/' . $this->getVersion() . '.sql'));
    }
}

<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use RuntimeException;

abstract class Migration extends AbstractMigration
{
    public function addEachSql(string $sqlFileContent): void
    {
        $sqls = array_filter(array_map('trim', explode(';', $sqlFileContent)));
        foreach ($sqls as $sql) {
            parent::addSql($sql);
        }
    }

    public function getVersion(): string
    {
        $fullClass = \get_class($this);
        $names = \explode('\\', $fullClass);
        $class = \end($names);

        preg_match('/^(Version)+(?P<version>\d{14})$/', $class, $matches);

        if ( ! isset($matches['version'])) {
            throw new RuntimeException('Version not found. Class: ' . $fullClass);
        }

        return $matches['version'];
    }
}

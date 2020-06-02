<?php
/**
 * Date: 12.04.2020
 * Time: 18:32
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use DateTime;
use kradwhite\db\exception\DbException;
use kradwhite\db\exception\PdoException;

/**
 * Class Migrations
 * @package kradwhite\migration\model
 */
class Migrations
{
    /** @var array */
    private array $migrations;

    /** @var MigrationRepository */
    private ?MigrationRepository $repository;

    /**
     * Migrations constructor.
     * @param array $migrations
     * @param MigrationRepository $repository
     */
    public function __construct(array $migrations, MigrationRepository $repository)
    {
        $this->migrations = $migrations;
        $this->repository = $repository;
    }

    /**
     * @return string
     * @throws MigrationException
     * @throws DbException
     */
    public function create(): string
    {
        return $this->repository->createTable();
    }

    /**
     * @param string $suffixName
     * @return string
     * @throws MigrationException
     */
    public function createMigration(string $suffixName): string
    {
        $className = date("_Y_m_d__H_i_s__") . $suffixName;
        return $this->repository->createFile($className, $this->getMigrationTemplate($className));
    }

    /**
     * @param int $count
     * @return void
     * @throws MigrationException
     * @throws PdoException
     */
    public function migrate(int $count)
    {
        $count = $this->calculateCount($count);
        foreach ($this->repository->loadMigrationNamesFromDirectory() as &$class) {
            if (!array_key_exists($class, $this->migrations)) {
                $this->repository->begin();
                $this->repository->buildMigration($class)->up();
                $this->repository->add(['name' => $class, 'date' => $this->obtainDateFromClass($class)]);
                $this->repository->commit();
                if (!--$count) {
                    break;
                }
            }
        }
    }

    /**
     * @param int $count
     * @return void
     * @throws MigrationException
     * @throws PdoException
     */
    public function rollback(int $count)
    {
        $count = $this->calculateCount($count);
        while ($this->migrations && $count-- > 0) {
            $migration = array_pop($this->migrations);
            $this->repository->begin();
            $this->repository->buildMigration($migration['name'])->down();
            $this->repository->removeById((int)$migration['id']);
            $this->repository->commit();
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->migrations);
    }

    /**
     * @return array
     * @throws MigrationException
     */
    public function getNamesNewMigrations(): array
    {
        $namesFromDir = $this->repository->loadMigrationNamesFromDirectory();
        $namesFromDb = array_column($this->migrations, 'name');
        return array_diff($namesFromDir, $namesFromDb);
    }

    /**
     * @param int $count
     * @return array
     */
    public function getLastMigrationsNames(int $count): array
    {
        if (!$count) {
            $count = 1;
        }
        $namesFromDb = array_column($this->migrations, 'name');
        return array_slice($namesFromDb, count($namesFromDb) - $count);
    }

    /**
     * @param string $class
     * @return string
     */
    private function obtainDateFromClass(string $class): string
    {
        return DateTime::createFromFormat("Y_m_d__H_i_s", substr($class, 1, 20))->format("Y-m-d H:i:s");
    }

    /**
     * @param int $count
     * @return int
     */
    private function calculateCount(int $count): int
    {
        return $count < 1 ? count($this->migrations) : $count;
    }

    /**
     * @param string $className
     * @return string
     */
    private function getMigrationTemplate(string $className): string
    {
        return <<<content
<?php

use kradwhite\migration\model\Migration;

/**
 * Class $className
 * @method conn(): Connection
 * @method table(): Table
 */
class $className extends Migration
{
    
    /**
     * @return void
     */
    public function up(): void
    {
        //\$table()->create();
    }
    
    /**
     * @return void
     */
    public function down(): void
    {
        //\$table()->drop();
    }
}
content;
    }
}
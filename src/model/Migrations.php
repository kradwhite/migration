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
     */
    public function migrate(int $count)
    {
        if (!$count) {
            $count = count($this->migrations);
        }
        $newMigrations = [];
        foreach ($this->repository->loadMigrationNamesFromDirectory() as &$class) {
            if (!array_key_exists($class, $this->migrations)) {
                $newMigrations[$class] = ['name' => $class, 'date' => $this->obtainDateFromClass($class)];
                $newMigrations[$class]['id'] = $this->repository->add($newMigrations[$class]);
                $this->repository->buildMigration($class)->up();
                if (!--$count) {
                    break;
                }
            }
        }
        $this->migrations = array_merge($this->migrations, $newMigrations);
        sort($this->migrations);
    }

    /**
     * @param int $count
     * @return void
     * @throws MigrationException
     */
    public function rollback(int $count)
    {
        if (!$count) {
            $count = count($this->migrations);
        }
        while ($count--) {
            $migration = array_pop($this->migrations);
            $this->repository->buildMigration($migration['name'])->down();
            $this->repository->removeById($migration['id']);
        }
    }

    /**
     * @param string $name
     * @return array
     * @throws MigrationException
     */
    public function rollbackByName(string $name): array
    {
        $candidatesKeys = [];
        foreach ($this->migrations as $key => &$migration) {
            if (strpos($name, $key) !== false) {
                $candidatesKeys[] = $key;
            }
        }
        if (!$candidatesKeys) {
            throw new MigrationException("Миграция, содержащая в имени '$name', не найдена");
        }
        return $this->rollbackAndRemove($candidatesKeys);
    }

    /**
     * @param int $id
     * @return void
     * @throws MigrationException
     */
    public function rollbackById(int $id)
    {
        foreach ($this->migrations as $key => &$migration) {
            if ($migration['id'] == $id) {
                $this->repository->buildMigration($migration['name'])->down();
                $this->repository->removeById($id);
                unset($this->migrations[$key]);
            }
        }
        throw new MigrationException("Миграция с идентификатором '$id' не найдена");
    }

    /**
     * @param string $datetime
     * @return array
     * @throws MigrationException
     */
    public function rollbackByDate(string $datetime): array
    {
        $candidatesKeys = [];
        foreach ($this->migrations as $key => &$migration) {
            if (strpos($datetime, $migration['date']) !== false) {
                $candidatesKeys[] = $key;
            }
        }
        if (!$candidatesKeys) {
            throw new MigrationException("Миграция, содержащая в дате '$datetime', не найдена");
        }
        return $this->rollbackAndRemove($candidatesKeys);
    }

    /**
     * @param array $candidatesKeys
     * @return array
     * @throws MigrationException
     */
    private function rollbackAndRemove(array $candidatesKeys): array
    {
        if (count($candidatesKeys) > 1) {
            return $candidatesKeys;
        }
        $this->repository->buildMigration($this->migrations[$candidatesKeys[0]]['name'])->down();
        $this->repository->removeById($this->migrations[$candidatesKeys[0]]['id']);
        unset($this->migrations[$this->migrations[$candidatesKeys[0]]]);
        return [];
    }

    /**
     * @param string $class
     * @return string
     */
    private function obtainDateFromClass(string $class): string
    {
        return DateTime::createFromFormat("Y_m_d__H_i_s", substr($class, 1, 21))->format("Y-m-d H:i:s");
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
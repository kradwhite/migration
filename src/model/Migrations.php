<?php
/**
 * Date: 12.04.2020
 * Time: 18:32
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

/**
 * Class Migrations
 * @package kradwhite\migration\model
 */
class Migrations
{
    /** @var array */
    private array $migrations = [];

    /** @var Config */
    private ?Config $config = null;

    /** @var MigrationRepository */
    private ?MigrationRepository $repository;

    /**
     * Migrations constructor.
     * @param array $migrations
     * @param Config $config
     * @param MigrationRepository $repository
     */
    public function __construct(array $migrations, Config $config, MigrationRepository $repository)
    {
        $this->migrations = $migrations;
        $this->config = $config;
        $this->repository = $repository;
    }

    /**
     * @return void
     * @throws MigrationException
     */
    public function create()
    {
        $this->repository->createTable();
    }

    /**
     * @param int $count
     * @return void
     */
    public function migrate(int $count)
    {

    }

    /**
     * @param int $count
     * @return void
     * @throws MigrationException
     */
    public function rollback(int $count)
    {
        while ($count--) {
            $migration = array_pop($this->migrations);
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
        return $this->removeById($candidatesKeys);
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
        return $this->removeById($candidatesKeys);
    }

    /**
     * @param array $candidatesKeys
     * @return array
     * @throws MigrationException
     */
    private function removeById(array $candidatesKeys): array
    {
        if (count($candidatesKeys) > 1) {
            return $candidatesKeys;
        }
        $this->repository->removeById($this->migrations[$candidatesKeys[0]]);
        unset($this->migrations[$this->migrations[$candidatesKeys[0]]]);
        return [];
    }

    /**
     * @return array
     * @throws MigrationException
     */
    private function getMigrationNamesForDirectory(): array
    {
        $dirname = $this->config->getPath();
        if (!file_exists($dirname)) {
            throw new MigrationException("Каталог с миграциями '$dirname' не найден");
        } else if (!is_dir($dirname)) {
            throw new MigrationException("Файл с имененм '$dirname' не является каталогом");
        }
        $filenames = scandir($dirname,);
        if ($filenames === false) {
            throw new MigrationException("Ошибка получения файлов из каталога '$dirname'");
        }
        $result = [];
        foreach ($filenames as &$filename) {
            
        }
        return $result;
    }
}
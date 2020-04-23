<?php
/**
 * Date: 12.04.2020
 * Time: 18:34
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use kradwhite\db\Connection;
use kradwhite\db\exception\DbException;

/**
 * Class MigrationRepository
 * @package model
 */
class MigrationRepository
{
    /** @var Connection */
    private ?Connection $connection = null;

    /** @var Config */
    private ?Config $config = null;

    /**
     * MigrationRepository constructor.
     * @param Connection|null $connection
     * @param Config|null $config
     */
    public function __construct(?Connection $connection, ?Config $config)
    {
        $this->connection = $connection;
        $this->config = $config;
    }

    /**
     * @return string
     * @throws MigrationException
     * @throws DbException
     */
    public function createTable(): string
    {
        $this->connection->begin();
        $this->connection->table($this->config->getMigrationTable())
            ->addColumn('id', 'bigauto', ['null' => false])
            ->addColumn('date', 'datetime', ['null' => false])
            ->addColumn('name', 'string', ['null' => false, 'limit' => 256])
            ->primaryKey('id')
            ->addIndex(['date'])
            ->addIndex(['name'], ['unique' => true])
            ->create();
        $this->connection->table($this->config->getMigrationTable())->insert(['date' => date("Y-m-d H:i:s"), 'name' => 'init']);
        $this->connection->commit();
        return $this->config->getMigrationTable();
    }

    /**
     * @return Migrations
     * @throws MigrationException
     */
    public function loadMigrations(): Migrations
    {
        try {
            $raw = $this->connection->selectMultiple($this->config->getMigrationTable(), [], [])->prepareExecute('assoc', ['name']);
            $attributes = [];
            foreach ($raw as &$migration) {
                $attributes[$migration['name']] = $migration;
            }
            return new Migrations($attributes, $this);
        } catch (DbException $e) {
            throw new MigrationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $attributes
     * @return int
     * @throws MigrationException
     */
    public function add(array $attributes): int
    {
        try {
            return (int)$this->connection->insert($this->config->getMigrationTable(), $attributes)->prepareExecute();
        } catch (DbException $e) {
            throw new MigrationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $className
     * @param string $content
     * @return string
     * @throws MigrationException
     */
    public function createFile(string $className, string $content): string
    {
        $fileName = $this->config->getPath() . "/$className.php";
        if (file_exists($fileName)) {
            throw new MigrationException("Миграция с именем '$fileName' уже существует");
        } else if (!file_put_contents($fileName, $content)) {
            throw new MigrationException("Ошибка создания файла '$fileName'");
        } else if (!chmod($fileName, 0664)) {
            throw new MigrationException("Ошибка установки доступов 0664 на файл '$fileName'");
        }
        return $fileName;
    }

    /**
     * @param int $id
     * @return void
     * @throws MigrationException
     */
    public function removeById(int $id)
    {
        try {
            $this->connection->delete($this->config->getMigrationTable(), ['id' => $id])->prepareExecute();
        } catch (DbException $e) {
            throw new MigrationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $className
     * @return Migration
     * @throws MigrationException
     */
    public function buildMigration(string $className): Migration
    {
        require $this->config->getPath() . "/$className.php";
        if (!class_exists($className)) {
            throw new MigrationException("Не найден класс '$className' миграции");
        } else if (!is_a($className, Migration::class)) {
            throw new MigrationException("Миграция должна быть унаследована от 'kradwhite\migration\Migration::class");
        }
        return new $className($this->connection);
    }

    /**
     * @return array
     * @throws MigrationException
     */
    public function loadMigrationNamesFromDirectory(): array
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
            if (preg_match(`_\d{4}_\d{2}_\d{2}__\d{2}_\d{2}_\d{2}__[A-Za-z_]{1,}.php`, $filename)) {
                $result[] = substr($filename, 0, -4);
            }
        }
        return $result;
    }

    public function begin()
    {
        $this->connection->begin();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollback()
    {
        $this->connection->rollback();
    }
}
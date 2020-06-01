<?php
/**
 * Date: 12.04.2020
 * Time: 18:34
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use kradwhite\db\Connection;
use kradwhite\db\exception\BeforeQueryException;
use kradwhite\db\exception\DbException;
use kradwhite\db\exception\PdoException;
use kradwhite\db\exception\PdoStatementException;

/**
 * Class MigrationRepository
 * @package model
 */
class MigrationRepository
{
    /** @var Connection */
    private Connection $connection;

    /** @var Config */
    private Config $config;

    /**
     * MigrationRepository constructor.
     * @param Connection $connection
     * @param Config $config
     */
    public function __construct(Connection $connection, Config $config)
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
            ->addColumn('date', 'timestamp', ['null' => false])
            ->addColumn('name', 'string', ['null' => false, 'limit' => 256])
            ->primaryKey('id')
            ->addIndex(['date'])
            ->addIndex(['name'], ['unique' => true])
            ->create();
        $this->connection->table($this->config->getMigrationTable())
            ->insert(['date' => date("Y-m-d H:i:s"), 'name' => 'init']);
        $this->connection->commit();
        return $this->config->getMigrationTable();
    }

    /**
     * @return Migrations
     * @throws MigrationException
     * @throws PdoException
     * @throws PdoStatementException
     * @throws BeforeQueryException
     */
    public function loadMigrations(): Migrations
    {
        $attributes = [];
        if ($this->isTableExist()) {
            $raw = $this->connection->selectMultiple($this->config->getMigrationTable(), [], [])
                ->prepareExecute('assoc', ['name']);
            foreach ($raw as &$migration) {
                $attributes[$migration['name']] = $migration;
            }
            unset($attributes['init']);
        }
        return new Migrations($attributes, $this);
    }

    /**
     * @param array $attributes
     * @return void
     * @throws MigrationException
     * @throws PdoException
     * @throws PdoStatementException
     */
    public function add(array $attributes)
    {
        $this->connection->insert($this->config->getMigrationTable(), $attributes)->prepareExecute();
    }

    /**
     * @param string $className
     * @param string $content
     * @return string
     * @throws MigrationException
     */
    public function createFile(string $className, string $content): string
    {
        $fileName = $this->config->getPath() . "$className.php";
        if (file_exists($fileName)) {
            throw new MigrationException('migration-already-exist', [$fileName]);
        } else if (!file_put_contents($fileName, $content)) {
            throw new MigrationException('migration-file-create-error', [$fileName]);
        } else if (!chmod($fileName, 0664)) {
            throw new MigrationException('migration-file-chmod-error', [$fileName]);
        }
        return $fileName;
    }

    /**
     * @param int $id
     * @return void
     * @throws MigrationException
     * @throws PdoException
     * @throws PdoStatementException
     */
    public function removeById(int $id)
    {
        $this->connection->delete($this->config->getMigrationTable(), ['id' => $id])->prepareExecute();
    }

    /**
     * @param string $className
     * @return Migration
     * @throws MigrationException
     */
    public function buildMigration(string $className): Migration
    {
        $filename = $this->config->getPath() . "$className.php";
        if (!file_exists($filename)) {
            throw new MigrationException('migration-file-not-found', [$filename]);
        }
        require $filename;
        if (!class_exists($className)) {
            throw new MigrationException('migration-class-not-found', [$className]);
        } else if (!is_a($className, Migration::class, true)) {
            throw new MigrationException('migration-not-is-a-migration', [$className]);
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
            throw new MigrationException('migration-dir-not-found', [$dirname]);
        } else if (!is_dir($dirname)) {
            throw new MigrationException('migration-dir-not-dir', [$dirname]);
        } else if (($filenames = scandir($dirname, SCANDIR_SORT_DESCENDING)) === false) {
            throw new MigrationException('migration-dir-scan-error', [$dirname]);
        }
        $result = [];
        foreach ($filenames as &$filename) {
            if (preg_match("/_\d{4}_\d{2}_\d{2}__\d{2}_\d{2}_\d{2}__[a-zA-Z1-9_]{1,}.php/", $filename)) {
                $result[] = substr($filename, 0, -4);
            }
        }
        return $result;
    }

    /**
     * @return void
     * @throws PdoException
     */
    public function begin()
    {
        $this->connection->begin();
    }

    /**
     * @return void
     * @throws PdoException
     */
    public function commit()
    {
        if ($this->connection->inTransaction()) {
            $this->connection->commit();
        }
    }

    /**
     * @return bool
     * @throws DbException
     * @throws MigrationException
     */
    private function isTableExist(): bool
    {
        $tables = $this->connection->meta()->tables($this->config->getEnvironment()['dbName']);
        return in_array($this->config->getMigrationTable(), $tables);
    }
}
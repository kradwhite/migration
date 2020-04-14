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
     * @return void
     * @throws MigrationException
     */
    public function createTable()
    {
        try {
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
        } catch (DbException $e) {
            $this->connection->rollback();
            throw new MigrationException($e->getMessage(), $e->getCode(), $e);
        }
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
            return new Migrations($attributes, $this->config, $this);
        } catch (DbException $e) {
            throw new MigrationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $attributes
     * @return void
     * @throws MigrationException
     */
    public function add(array $attributes)
    {
        try {
            $this->connection->insert($this->config->getMigrationTable(), $attributes)->prepareExecute();
        } catch (DbException $e) {
            throw new MigrationException($e->getMessage(), $e->getCode(), $e);
        }
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
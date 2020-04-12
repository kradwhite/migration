<?php
/**
 * Date: 12.04.2020
 * Time: 18:24
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use kradwhite\db\Connection;
use kradwhite\db\driver\Driver;
use kradwhite\db\driver\DriverFactory;
use kradwhite\db\exception\BeforeQueryException;

/**
 * Class App
 * @package model
 */
class App
{
    /** @var array */
    private array $objects = [];

    /**
     * App constructor.
     * @param array $config
     * @param string $environment
     * @throws MigrationException
     */
    public function __construct(array $config, string $environment)
    {
        $this->objects[Config::class] = new Config($config, $environment);
    }

    /**
     * @return Config
     */
    public function config(): Config
    {
        return $this->objects[Config::class];
    }

    /**
     * @return Migrations
     * @throws MigrationException
     */
    public function migrations(): Migrations
    {
        return $this->migrationRepository()->loadMigrations();
    }

    /**
     * @return Connection
     * @throws MigrationException
     */
    private function connection(): Connection
    {
        if (!isset($this->objects[Connection::class])) {
            $this->objects[Connection::class] = new Connection($this->getDriver());
        }
        return $this->objects[Connection::class];
    }

    /**
     * @return MigrationRepository
     * @throws MigrationException
     */
    private function migrationRepository(): MigrationRepository
    {
        if (!isset($this->objects[MigrationRepository::class])) {
            $this->objects[MigrationRepository::class] = new MigrationRepository($this->connection(), $this->config());
        }
        return $this->objects[MigrationRepository::class];
    }

    /**
     * @return Driver
     * @throws MigrationException
     */
    private function getDriver(): Driver
    {
        try {
            return DriverFactory::buildFromArray($this->config()->getDriver(), $this->config()->getEnvironment());
        } catch (BeforeQueryException $e) {
            throw new MigrationException("Ошибка создания драйвера: " . $e->getMessage(), 0, $e);
        }
    }
}
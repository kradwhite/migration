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
use kradwhite\language\Lang;
use kradwhite\language\LangException;

/**
 * Class App
 * @package model
 */
class App
{
    /** @var array */
    private array $objects = [];

    /** @var Lang */
    private static ?Lang $lang = null;

    /**
     * App constructor.
     * @param string $configFilename
     * @throws MigrationException
     */
    public function __construct(string $configFilename)
    {
        $this->objects[Config::class] = $this->buildConfig($configFilename);
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
    public function connection(): Connection
    {
        if (!isset($this->objects[Connection::class])) {
            $this->objects[Connection::class] = new Connection($this->getDriver());
        }
        return $this->objects[Connection::class];
    }

    /**
     * @return Lang
     * @throws LangException
     */
    public static function lang(): Lang
    {
        if (!self::$lang) {
            $configFilename = __DIR__ . '/../../language.php';
            $rawLocale = (string)getenv('LANG');
            $locale = substr($rawLocale, 0, 2);
            self::$lang = Lang::init($configFilename, $locale);
        }
        return self::$lang;
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
            return DriverFactory::buildFromArray($this->config()->getEnvironment());
        } catch (BeforeQueryException $e) {
            throw new MigrationException("driver-create-error", [$e->getMessage()], $e);
        }
    }

    /**
     * @param string $filename
     * @return Config
     * @throws MigrationException
     */
    private function buildConfig(string $filename): Config
    {
        if (!file_exists($filename)) {
            $filename = __DIR__ . '/../' . Config::Name;
        }
        if (!$config = yaml_parse_file($filename)) {
            throw new MigrationException('config-file-wrong', [$filename]);
        }
        return new Config($config);
    }
}
<?php
/**
 * Date: 12.04.2020
 * Time: 17:46
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use kradwhite\config\ConfigException;

/**
 * Class Config
 * @package model
 */
class Config
{
    /** @var string */
    const Name = 'migrations.yml';

    /** @var array */
    private array $config = [];

    /** @var string */
    private string $environment = '';

    /**
     * Config constructor.
     * @param array $config
     * @throws MigrationException
     */
    public function __construct(array $config)
    {
        if (!isset($config['defaults']['environment']) || !$config['defaults']['environment']) {
            throw new MigrationException('environment-not-found');
        }
        $environment = $config['defaults']['environment'];
        $this->config = $config;
        $this->environment = $environment;
    }

    /**
     * @param string $path
     * @param string $locale
     * @return string
     * @throws ConfigException
     */
    public function create(string $path, string $locale): string
    {
        $source = __DIR__ . '/../../config';
        (new \kradwhite\config\Config($source))->build($path, $locale);
        return $path . self::Name;
    }

    /**
     * @return array
     * @throws MigrationException
     */
    public function getEnvironment(): array
    {
        if (!isset($this->config['environments'][$this->environment])) {
            throw new MigrationException('config-file-environment-not-found', [$this->environment]);
        }
        return $this->config['environments'][$this->environment];
    }

    /**
     * @return string
     * @throws MigrationException
     */
    public function getMigrationTable(): string
    {
        if (isset($this->config['environments'][$this->environment]['table'])) {
            return $this->config['environments'][$this->environment]['table'];
        } else if (isset($this->config['defaults']['table'])) {
            return $this->config['defaults']['table'];
        }
        throw new MigrationException('table-name-not-found');
    }

    /**
     * @return string
     * @throws MigrationException
     */
    public function getPath(): string
    {
        if (!isset($this->config['paths']['migrations'])) {
            throw new MigrationException('migration-path-not-found');
        }
        $path = $this->getWorkPath();
        return $path . $this->config['paths']['migrations'];
    }

    /**
     * @return string
     * @throws MigrationException
     */
    public function getDriver(): string
    {
        if (!isset($this->config['environments'][$this->environment]['driver'])) {
            throw new MigrationException('driver-not-found', [$this->environment]);
        }
        return $this->config['environments'][$this->environment]['driver'];
    }

    /**
     * @return string
     * @throws MigrationException
     */
    private function getWorkPath(): string
    {
        if (!$path = getcwd()) {
            throw new MigrationException('work-dir-wrong');
        }
        if ($path[strlen($path) - 1] != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        return $path;
    }
}
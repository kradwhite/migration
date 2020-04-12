<?php
/**
 * Date: 12.04.2020
 * Time: 17:46
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

/**
 * Class Config
 * @package model
 */
class Config
{
    /** @var string */
    const Name = 'migration.yml';

    /** @var array */
    private array $config = [];

    /** @var string */
    private string $environment = '';

    /**
     * Config constructor.
     * @param array $config
     * @param string $environment
     * @throws MigrationException
     */
    public function __construct(array $config, string $environment)
    {
        if (!$environment) {
            if (!isset($config['defaults']['environment']) && !$config['defaults']['environment']) {
                throw new MigrationException("Не указана environment..");
            }
            $environment = $config['defaults']['environment'];
        }
        $this->config = $config;
        $this->environment = $environment;
    }

    /**
     * @return void
     * @throws MigrationException
     */
    public function create()
    {
        $name = $this->getWorkPath() . DIRECTORY_SEPARATOR . self::Name;
        if (file_exists($name)) {
            throw new MigrationException("Файл конфигурации '$name' уже существует");
        } else if (!yaml_emit_file($name, $this->config)) {
            throw new MigrationException("Ошибка создания файла конфигурации '$name'");
        }
    }

    /**
     * @return array
     * @throws MigrationException
     */
    public function getEnvironment(): array
    {
        if (!isset($this->config['environments'][$this->environment])) {
            throw new MigrationException("");
        }
        return $this->config['environments'][$this->environment];
    }

    /**
     * @return string
     * @throws MigrationException
     */
    public function getMigrationTable(): string
    {
        if (isset($this->config['environments'][$this->environment]['migration_table'])) {
            return $this->config['environments'][$this->environment]['migration_table'];
        } else if (isset($this->config['defaults']['migration_table'])) {
            return $this->config['defaults']['migration_table'];
        }
        throw new MigrationException("Не указано имя таблицы с миграциями");
    }

    /**
     * @return string
     * @throws MigrationException
     */
    public function getPath(): string
    {
        if (!isset($this->config['paths']['migrations'])) {
            throw new MigrationException("Не указан путь до каталога с миграциями");
        }
        $path = $this->getWorkPath();
        if ($path[strlen($path) - 1] != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        return $path . $this->config['paths']['migrations'];
    }

    /**
     * @return string
     * @throws MigrationException
     */
    public function getDriver(): string
    {
        if (!isset($this->config['environments'][$this->environment]['driver'])) {
            throw new MigrationException("Не указан драйвер внутри '{$this->environment}' environment");
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
            throw new MigrationException("Ошика получения рабочего каталога. Возмножно нехватает доступа на чтение у одно из каталогов пути");
        }
        return $path;
    }
}
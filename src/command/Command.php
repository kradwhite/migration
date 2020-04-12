<?php
/**
 * Date: 12.04.2020
 * Time: 14:51
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\db\Connection;
use kradwhite\migration\MigrationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package command
 */
class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * @return string
     */
    protected function getTemplateConfigFileName(): string
    {
        return dirname(__DIR__) . '/config.yml';
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getConfigFileName(InputInterface $input): string
    {
        if (!$workDir = $input->getOption('path')) {
            $workDir = getcwd();
        } else if (!$workDir) {
            return '';
        }
        if ($workDir[strlen($workDir) - 1] != DIRECTORY_SEPARATOR) {
            $workDir .= DIRECTORY_SEPARATOR;
        }
        return $workDir . 'migration.yml';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function loadConfig(InputInterface $input, OutputInterface $output): array
    {
        if (!$configFileName = $this->getConfigFileName($input)) {
            $output->writeln('<fg=red>Ошика получения рабочего каталога. Возмножно нехватает доступа на чтение у одно из каталогов в цепочке.</>');
        } else if (!file_exists($configFileName)) {
            $output->writeln("<fg=red>Файл конфигурации '$configFileName' не найден.</>");
        } else if (!$config = yaml_parse_file($configFileName, -1)) {
            $output->writeln("<fg=red>Ошибка чтения файла конфигурации '$configFileName'</>");
        } else {
            return $config;
        }
        return [];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $config
     * @return array
     */
    protected function getEnvironmentConfig(InputInterface $input, OutputInterface $output, array $config): array
    {
        if (!$environment = $input->getOption('environment')) {
            $environment = isset($config['defaults']['environment']) ?? '';
        }
        if (!$environment) {
            $output->writeln("<fg=red>Не выбран environment...</>");
        } else if (!isset($config['environments'][$environment])) {
            $output->writeln("<fg=red>Не найден environment с именем '$environment'</>");
        } else {
            return $config['environments'][$environment];
        }
        return [];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $environment
     * @return Connection|null
     */
    protected function getConnection(InputInterface $input, OutputInterface $output): ?Connection
    {
        if (!$config = $this->loadConfig($input, $output)) {
            return null;
        } else if (!$environment = $this->getEnvironmentConfig($input, $output, $config)) {
            return null;
        } else {
        }
    }
}
<?php
/**
 * Date: 12.04.2020
 * Time: 14:51
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\db\exception\DbException;
use kradwhite\db\exception\PdoException;
use kradwhite\migration\model\App;
use kradwhite\migration\model\Config;
use kradwhite\migration\model\MigrationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package command
 */
abstract class Command extends \Symfony\Component\Console\Command\Command
{
    /** @var App */
    private ?App $app = null;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return App|null
     * @throws MigrationException
     */
    protected function buildApp(InputInterface $input, OutputInterface $output): ?App
    {
        if (!$this->app) {
            if (!$target = $this->getConfigFileName($input)) {
                $output->writeln('<fg=red>Ошика получения рабочего каталога. Возмножно нехватает доступа на чтение у одно из каталогов в цепочке.</>');
            }
            $this->app = $target ? new App($target) : null;
        }
        return $this->app;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getConfigFileName(InputInterface $input): string
    {
        if (!$path = $input->getOption('path') ?? getcwd()) {
            return '';
        } else if ($path[0] != DIRECTORY_SEPARATOR) {
            if (!$prefix = getcwd()) {
                return '';
            }
            $path = $prefix . DIRECTORY_SEPARATOR . $path;
        }
        if ($path[strlen($path) - 1] != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        return $path . Config::Name;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws MigrationException
     * @throws PdoException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->doExecute($input, $output);
            $output->writeln("<fg=green>Успешно выполнено</>");
        } catch (MigrationException|DbException $e) {
            if ($this->app && $this->app->connection()->inTransaction()) {
                $this->app->connection()->rollback();
            }
            $output->writeln("<fg=red>{$e->getMessage()}</>");
        }
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addOption('path', 'p', InputOption::VALUE_OPTIONAL,
            'Путь хранения файла конфигурации миграций')
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL,
                'Устанавливает environment с настройками базы данных');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output);
}
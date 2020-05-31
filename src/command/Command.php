<?php
/**
 * Date: 12.04.2020
 * Time: 14:51
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\db\exception\DbException;
use kradwhite\language\LangException;
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
                $output->writeln(App::lang()->phrase('messages', 'work-dir-wrong'));
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
        if (!$path = $input->getOption('path') ? $input->getOption('path') : getcwd()) {
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->doExecute($input, $output);
            $output->writeln(App::lang()->phrase('messages', 'success-execute'));
        } catch (MigrationException|DbException|LangException $e) {
            if ($this->app && $this->app->connection()->inTransaction()) {
                $this->app->connection()->rollback();
            }
            $output->writeln(App::lang()->phrase('messages', 'exception-message'));
        }
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addOption('path', 'p', InputOption::VALUE_OPTIONAL,
            App::lang()->phrase('messages', 'option-path-description'))
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL,
                App::lang()->phrase('messages', 'option-environment-description'));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output);
}
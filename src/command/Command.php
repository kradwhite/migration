<?php
/**
 * Date: 12.04.2020
 * Time: 14:51
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\config\ConfigException;
use kradwhite\db\exception\DbException;
use kradwhite\language\LangException;
use kradwhite\language\text\Text;
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

    /** @var string */
    private string $oldChdir = '';

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
        $path = $input->getOption('path');
        if ($path[0] != DIRECTORY_SEPARATOR) {
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = $this->doExecute($input, $output);
            $output->writeln(App::lang()->phrase('messages', 'success-execute'));
            return $result;
        } catch (MigrationException|DbException|LangException|ConfigException $e) {
            if ($this->app && $this->app->connection()->inTransaction()) {
                $this->app->connection()->rollback();
            }
            $output->writeln(App::lang()->phrase('messages', 'exception-message', [$e->getMessage()]));
        } finally {
            if ($this->oldChdir) {
                chdir($this->oldChdir);
            }
        }
        return 1;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $messages = App::lang()->text('messages');
        $this->addOption('path', 'p', InputOption::VALUE_OPTIONAL,
            $messages->phrase('option-path-description'), getcwd())
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL,
                $messages->phrase('option-environment-description'))
            ->addOption('yes', 'y', InputOption::VALUE_OPTIONAL,
                $messages->phrase('option-yes'), 'no');
    }

    /**
     * @param InputInterface $input
     */
    protected function setChdir(InputInterface $input)
    {
        $path = $input->getOption('path');
        $this->oldChdir = getcwd();
        if ($path[0] != DIRECTORY_SEPARATOR) {
            $path = $this->oldChdir . DIRECTORY_SEPARATOR . $path;
        }
        chdir($path);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $names
     * @param Text $messages
     * @return string
     * @throws LangException
     */
    protected function getAnswer(InputInterface $input, OutputInterface $output, array $names, Text $messages): string
    {
        foreach ($names as &$name) {
            $output->writeln($name);
        }
        $output->write($messages->phrase('migrate-question'));
        $answer = $input->getOption('yes') ? '' : 'y';
        while (!strlen($answer) || !in_array($answer[0], ['y', 'n', 'Y', 'N'])) {
            $answer = fgets(STDIN);
        }
        return $answer;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output);
}
<?php
/**
 * Date: 11.04.2020
 * Time: 13:25
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\migration\model\App;
use kradwhite\migration\model\Config;
use kradwhite\migration\model\MigrationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateConfigCommand
 * @package kradwhite\migration\command
 */
class ConfigCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'config';

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setDescription(App::lang()->phrase('messages', 'config-command-description'))
            ->setHelp(App::lang()->phrase('messages', 'config-command-help'))
            ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL,
                App::lang()->phrase('messages', 'option-locale-description'), 'en');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MigrationException
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        if ($app = $this->buildApp($input, $output)) {
            $filename = $app->config()->create($input->getOption('path'), $input->getOption('locale'));
            $output->writeln($filename);
        }
        return (int)!$app;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return App
     * @throws MigrationException
     */
    protected function buildApp(InputInterface $input, OutputInterface $output): App
    {
        $ds = DIRECTORY_SEPARATOR;
        $configFilename = __DIR__ . "$ds..$ds..{$ds}config{$ds}templates$ds" . Config::Name;
        return new App($configFilename);
    }
}
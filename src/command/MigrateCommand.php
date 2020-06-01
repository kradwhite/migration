<?php
/**
 * Date: 23.04.2020
 * Time: 20:49
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\migration\model\App;
use kradwhite\migration\model\MigrationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateCommand
 * @package command
 */
class MigrateCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'migrate';

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $messages = App::lang()->text('messages');
        $this->setDescription($messages->phrase('migrate-command-description'))
            ->setHelp($messages->phrase('migrate-command-help'))
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL,
                $messages->phrase('migrate-command-count'));
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
            $this->setChdir($input);
            $app->migrations()->migrate((int)$input->getOption('count'));
        }
        return (int)!$app;
    }
}
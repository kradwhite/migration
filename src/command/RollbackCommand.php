<?php
/**
 * Date: 23.04.2020
 * Time: 21:13
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
 * Class RollbackCommand
 * @package command
 */
class RollbackCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'rollback';

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $messages = App::lang()->text('messages');
        $this->setDescription($messages->phrase('rollback-command-description'))
            ->setHelp($messages->phrase('rollback-command-help'))
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL,
                $messages->phrase('rollback-command-count'));
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
            $messages = App::lang()->text('messages');
            if ($names = $app->migrations()->getLastMigrationsNames((int)$input->getOption('count'))) {
                $output->writeln($messages->phrase('rollback-title'));
                $answer = $this->getAnswer($input, $output, $names, $messages);
                if ($answer && in_array($answer[0], ['y', 'Y'])) {
                    $app->migrations()->rollback((int)$input->getOption('count'));
                }
            } else {
                $output->writeln(App::lang()->phrase('messages', 'not-exist-rollback-migrations'));
            }
        }
        return (int)!$app;
    }
}
<?php
/**
 * Date: 23.04.2020
 * Time: 21:04
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\migration\model\App;
use kradwhite\migration\model\MigrationException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateCommand
 * @package command
 */
class CreateCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'create';

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $messages = App::lang()->text('messages');
        $this->setDescription($messages->phrase('create-command-description'))
            ->setHelp($messages->phrase('create-command-help'))
            ->addArgument('name', InputArgument::REQUIRED, $messages->phrase('create-command-name'));
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
            $name = $app->migrations()->createMigration((string)$input->getArgument('name'));
            $output->writeln($name);
        }
        return (int)!$app;
    }
}
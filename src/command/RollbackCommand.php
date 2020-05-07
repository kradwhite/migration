<?php
/**
 * Date: 23.04.2020
 * Time: 21:13
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

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
        $this->setDescription("Откат миграций")
            ->setHelp('Запускает откат миграций')
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL,
                'Колличество миграций, которые будут выполены');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws MigrationException
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        if ($app = $this->buildApp($input, $output)) {
            $app->migrations()->rollback((int)$input->getOption('count'));
        }
    }
}
<?php
/**
 * Date: 23.04.2020
 * Time: 20:49
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

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
        $this->setDescription("<fg=green>Применение миграций</>")
            ->setHelp('<fg=green>Запускает выполение миграций</>')
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL,
                '<fg=green>Колличество миграция, которые будут выполены</>');
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
            $app->migrations()->migrate((int)$input->getOption('count'));
        }
    }
}
<?php
/**
 * Date: 23.04.2020
 * Time: 20:49
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace command;

use kradwhite\db\exception\PdoException;
use kradwhite\migration\command\Command;
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
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription("<fg=green>Применение миграций</>")
            ->setHelp('<fg=green>Запускает выполение миграций</>')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL,
                '<fg=green>Устанавливает каталог с файлом конфигурации.</>')
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL,
                '<fg=green>Колличество миграция, которые будут выполены');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws PdoException
     * @throws MigrationException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->transactionExecute(function () use ($input, $output) {
            if ($app = $this->buildApp($input, $output)) {
                $app->migrations()->migrate((int)$input->getOption('count'));
            }
        }, $output);
        return 0;
    }
}
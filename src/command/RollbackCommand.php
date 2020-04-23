<?php
/**
 * Date: 23.04.2020
 * Time: 21:13
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
 * Class RollbackCommand
 * @package command
 */
class RollbackCommand extends Command
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
                '<fg=green>Колличество миграция, которые будут выполены</>')
            ->addOption('name', 'n', InputOption::VALUE_OPTIONAL,
                '<fg=green>Часть имени миграции</>')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL,
                '<fg=green>Дата миграции</>')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL,
                '<fg=green>Идентификатор миграции</>');
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
                do {

                } while (is_array($names));
                $name = $app->migrations()->createMigration((string)$input->getOption('name'));
                $output->writeln($name);
            }
        }, $output);
        return 0;
    }
}
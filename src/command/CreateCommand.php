<?php
/**
 * Date: 23.04.2020
 * Time: 21:04
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
 * Class CreateCommand
 * @package command
 */
class CreateCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription("<fg=green>Создание миграции</>")
            ->setHelp('<fg=green>Создаёт миграцию</>')
            ->addUsage('<fg=green>Имя миграции')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL,
                '<fg=green>Устанавливает каталог с файлом конфигурации.</>');
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
                $name = $app->migrations()->createMigration((string)$input->getOption('name'));
                $output->writeln($name);
            }
        }, $output);
        return 0;
    }
}
<?php
/**
 * Date: 12.04.2020
 * Time: 14:34
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\db\exception\PdoException;
use kradwhite\migration\model\MigrationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TableCommand
 * @package command
 */
class TableCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'table';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('<fg=green>Создание таблицы для хранения миграций.</>')
            ->setHelp('<fg=green>Создаёт таблицу для хранения миграций.</>')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL,
                '<fg=green>Устанавливает каталог с файлом конфигурации.</>')
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL,
                '<fg=green>Устанавливает environment с настройками базы данных.</>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MigrationException
     * @throws PdoException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->transactionExecute(function () use ($input, $output) {
            if ($app = $this->buildApp($input, $output)) {
                $app->migrations()->create();
            }
        }, $output);
        return 0;
    }
}
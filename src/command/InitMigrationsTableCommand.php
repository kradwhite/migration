<?php
/**
 * Date: 12.04.2020
 * Time: 14:34
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitMigrationsTableCommand
 * @package command
 */
class InitMigrationsTableCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'table';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('<fg=green>Создание таблицы для хранения миграций...</>')
            ->setHelp('<fg=green>Создаёт таблицу для хранения для миграций.</>')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL,
                '<fg=green>Задаёт каталог с файлом конфигурации.</>')
            ->addOption('environment', null, InputOption::VALUE_OPTIONAL,
                '<fg=green>Задаёт environment с настройками базы данных.</>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->getConnection($input, $output);
        return 0;
    }
}
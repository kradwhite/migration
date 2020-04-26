<?php
/**
 * Date: 12.04.2020
 * Time: 14:34
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\db\exception\DbException;
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
        parent::configure();
        $this->setDescription('<fg=green>Создание таблицы для хранения миграций.</>')
            ->setHelp('<fg=green>Создаёт таблицу для хранения миграций.</>')
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL,
                '<fg=green>Устанавливает environment с настройками базы данных.</>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws MigrationException
     * @throws DbException
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        if ($app = $this->buildApp($input, $output)) {
            $app->migrations()->create();
        }
    }
}
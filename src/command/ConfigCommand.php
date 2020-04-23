<?php
/**
 * Date: 11.04.2020
 * Time: 13:25
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
 * Class CreateConfigCommand
 * @package kradwhite\migration\command
 */
class ConfigCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'config';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('<fg=green>Создание файла конфигурации миграций</>')
            ->setHelp('<fg=green>Создаёт файл конфигурации миграций</>')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL,
                '<fg=green>Путь хранения файла конфигурации миграций</>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            if ($app = $this->buildApp($input, $output)) {
                $filename = $app->config()->create();
                $output->writeln($filename);
                $output->writeln("Успешно создан");
            }
        } catch (MigrationException $e) {
            $output->writeln("<fg=red>{$e->getMessage()}");
        }
        return 0;
    }
}
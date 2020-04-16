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
        $this->setDescription('<fg=green>Создание конфиг файла миграций...</>')
            ->setHelp('<fg=green>Создаёт файл конфигурации для миграций.</>')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL,
                '<fg=green>Путь хранения конфиг файла миграций</>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$target = $this->getConfigFileName($input)) {
            $output->writeln('<fg=red>Ошика получения рабочего каталога. Возмножно нехватает доступа на чтение у одно из каталогов в цепочке.</>');
        } else if (file_exists($target)) {
            $output->writeln("<fg=red>Файл конфигурации '$target' уже существует...</>");
        } else if (!copy($source, $target)) {
            $output->writeln("<fg=red>Ошибка копирования файла конфигурации '$target'...</>");
        } else if (!chmod($target, '0664')) {
            $output->writeln("<fg=red>Ошибка установки прав 0664 на файл конфигурации '$target'</>");
        } else {
            $output->writeln("<fg=green>Файл конфигурации '$target' миграци успешно создан!</>");
        }
        return 0;
    }
}
<?php
/**
 * Date: 23.04.2020
 * Time: 21:04
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\migration\model\MigrationException;
use Symfony\Component\Console\Input\InputInterface;
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
        parent::configure();
        $this->setDescription("<fg=green>Создание миграции</>")
            ->setHelp('<fg=green>Создаёт миграцию</>')
            ->addUsage('<fg=green>Имя миграции');
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
            $name = $app->migrations()->createMigration((string)$input->getOption('name'));
            $output->writeln($name);
        }
    }
}
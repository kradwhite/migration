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
        parent::configure();
        $this->setDescription(App::lang()->phrase('messages', 'config-command-description'))
            ->setHelp(App::lang()->phrase('messages', 'config-command-help'));
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
            $filename = $app->config()->create();
            $output->writeln($filename);
        }
    }
}
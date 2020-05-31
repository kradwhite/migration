<?php
/**
 * Date: 12.04.2020
 * Time: 14:34
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\command;

use kradwhite\db\exception\DbException;
use kradwhite\migration\model\App;
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
        $messages = App::lang()->text('messages');
        $this->setDescription($messages->phrase('table-command-description'))
            ->setHelp($messages->phrase('table-command-help'))
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL,
                $messages->phrase('table-command-environment'));
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
<?php
/**
 * Date: 11.04.2020
 * Time: 13:21
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use command\CreateCommand;
use command\MigrateCommand;
use kradwhite\migration\command\ConfigCommand;
use kradwhite\migration\command\TableCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->addCommands([
    new ConfigCommand(),
    new TableCommand(),
    new CreateCommand(),
    new MigrateCommand(),
    new RollbackCommand(),
]);

$application->run();
<?php
/**
 * Date: 11.04.2020
 * Time: 13:21
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use kradwhite\migration\command\CreateConfigCommand;
use kradwhite\migration\command\InitMigrationsTableCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->addCommands([
    new CreateConfigCommand(),
    new InitMigrationsTableCommand(),
    new MigrateCommand(),
    new RollbackCommand(),
]);

$application->run();
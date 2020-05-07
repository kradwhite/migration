<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use kradwhite\db\Connection;
use kradwhite\db\driver\DriverFactory;

class PostgreSql extends \Codeception\Module
{
    public function conn(): Connection
    {
        return new Connection(DriverFactory::build('pgsql', 'pgsql', 'test-2', 'admin', 'admin'));
    }
}

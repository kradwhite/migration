<?php

namespace kradwhite\tests\CLI;

class MigrateCommandTest extends \Codeception\Test\Unit
{
    /**
     * @var \CLITester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testExecute()
    {
        $before = $this->tester->grabNumRecords('command-migrate');
        $this->tester->runShellCommand('php migration migrate -p tests/_data/MigrateCommandTest');
        $after = $this->tester->grabNumRecords('command-migrate');
        $this->assertEquals($before + 1, $after);
    }
}
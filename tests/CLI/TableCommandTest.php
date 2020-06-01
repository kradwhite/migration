<?php

namespace kradwhite\tests\CLI;

class TableCommandTest extends \Codeception\Test\Unit
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
        $this->tester->runShellCommand('php migration table -p tests/_data/TableCommandTest');
        $tables = $this->tester->conn()->meta()->tables('test-1');
        $this->assertContains('command-table', $tables);
    }
}
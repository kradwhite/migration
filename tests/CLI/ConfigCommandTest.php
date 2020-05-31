<?php

namespace kradwhite\tests\CLI;

use kradwhite\migration\model\Config;

class ConfigCommandTest extends \Codeception\Test\Unit
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
        $cd = getcwd();
        if (file_exists("$cd/tests/_data/ConfigCommandTest/" . Config::Name)) {
            $this->tester->deleteFile("$cd/tests/_data/ConfigCommandTest/" . Config::Name);
        }
        $this->tester->runShellCommand('php migration config -p tests/_data/ConfigCommandTest -l ru');
        $this->assertFileExists("$cd/tests/_data/ConfigCommandTest/" . Config::Name);
    }
}
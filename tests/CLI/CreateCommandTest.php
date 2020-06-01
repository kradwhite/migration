<?php

namespace kradwhite\tests\CLI;

class CreateCommandTest extends \Codeception\Test\Unit
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
        $migrations = getcwd() . '/tests/_data/CreateCommandTest/migrations';
        if(file_exists($migrations)) {
            $this->tester->deleteDir($migrations);
        }
        mkdir($migrations);
        chmod($migrations, 0777);
        $this->tester->runShellCommand('php migration create NameOfMigration -p tests/_data/CreateCommandTest');
        $files = scandir($migrations);
        $this->assertCount(3, $files);
    }
}
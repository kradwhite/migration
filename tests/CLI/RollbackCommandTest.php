<?php 
class RollbackCommandTest extends \Codeception\Test\Unit
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
        $before = $this->tester->grabNumRecords('command-rollback');
        $this->tester->runShellCommand('php migration rollback -p tests/_data/RollbackCommandTest -c 1');
        $after = $this->tester->grabNumRecords('command-rollback');
        $this->assertEquals($before - 1, $after);
    }
}
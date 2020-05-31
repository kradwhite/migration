<?php

namespace kradwhite\tests\unit;

use kradwhite\db\Connection;
use kradwhite\migration\model\Migration;
use kradwhite\migration\model\MigrationRepository;
use kradwhite\migration\model\Migrations;

class MigrationsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var MigrationRepository */
    private $repo;

    private int $countExecutedMigrations = 0;

    private array $executedMigrations = ['_2018_01_03__23_00_00__mig1' => ['name' => '', 'id' => 1],
        '_2019_01_03__23_00_00__mig2' => ['name' => '', 'id' => 2],
        '_2020_01_03__23_00_00__mig3' => ['name' => '', 'id' => 3]];

    protected function _before()
    {
        $this->countExecutedMigrations = 0;
        $this->repo = $this->make(MigrationRepository::class, [
            'loadMigrationNamesFromDirectory' => [
                '_2018_01_03__23_00_00__mig1',
                '_2019_01_03__23_00_00__mig2',
                '_2020_01_03__23_00_00__mig3'
            ],
            'begin' => null,
            'commit' => null,
            'buildMigration' => new class($this->make(Connection::class)) extends Migration {
            },
            'add' => function (array $stub) {
                ++$this->countExecutedMigrations;
                return 1;
            },
            'removeById' => function (int $stub) {
                ++$this->countExecutedMigrations;
                return 1;
            },
            'createTable' => 'table',
            'createFile' => function (string $className, string $content) {
                return $className;
            }
        ]);
    }

    protected function _after()
    {
    }

    // tests
    public function testCreate()
    {
        $result = (new Migrations([], $this->repo))->create();
        $this->assertEquals($result, 'table');
    }

    public function testCreateMigration()
    {
        $className = 'add_migration';
        $result = (new Migrations([], $this->repo))->createMigration($className);
        $this->assertEquals(date("_Y_m_d__H_i_s__") . $className, $result);
    }

    public function testMigrateCountZero()
    {
        (new Migrations([], $this->repo))->migrate(0);
        $this->assertEquals($this->countExecutedMigrations, 3);
    }

    public function testMigrateCountTwo()
    {
        (new Migrations([], $this->repo))->migrate(2);
        $this->assertEquals($this->countExecutedMigrations, 2);
    }

    public function testMigrateNothing()
    {
        (new Migrations($this->executedMigrations, $this->repo))->migrate(0);
        $this->assertEquals($this->countExecutedMigrations, 0);
    }

    public function testRollbackZero()
    {
        (new Migrations($this->executedMigrations, $this->repo))->rollback(0);
        $this->assertEquals($this->countExecutedMigrations, 3);
    }

    public function testRollbackCountTwo()
    {
        (new Migrations($this->executedMigrations, $this->repo))->rollback(2);
        $this->assertEquals($this->countExecutedMigrations, 2);
    }

    public function testRollbackNothing()
    {
        (new Migrations([], $this->repo))->rollback(2);
        $this->assertEquals($this->countExecutedMigrations, 0);
    }
}
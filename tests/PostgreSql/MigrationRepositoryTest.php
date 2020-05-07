<?php

namespace kradwhite\tests\PostgreSql;

use kradwhite\migration\model\Config;
use kradwhite\migration\model\Migration;
use kradwhite\migration\model\MigrationException;
use kradwhite\migration\model\MigrationRepository;
use kradwhite\migration\model\Migrations;

class MigrationRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \MySqlTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testCreateTable()
    {
        $config = $this->make(Config::class, ['getMigrationTable' => 'migrations-create-table']);
        $tableName = (new MigrationRepository($this->tester->conn(), $config))->createTable();
        $this->assertEquals($tableName, 'migrations-create-table');
        $this->assertEquals($this->tester->grabNumRecords('migrations-create-table'), 1);
    }

    public function testLoadMigrations()
    {
        $config = $this->make(Config::class, ['getMigrationTable' => 'migrations-load']);
        $migrations = (new MigrationRepository($this->tester->conn(), $config))->loadMigrations();
        $this->assertInstanceOf(Migrations::class, $migrations);
        $this->assertEquals($migrations->count(), 2);
    }

    public function testAdd()
    {
        $config = $this->make(Config::class, ['getMigrationTable' => 'migrations-load']);
        $attributes = ['date' => date('Y-m-d H:i:s'), 'name' => 'name'];
        $before = $this->tester->grabNumRecords('migrations-load');
        (new MigrationRepository($this->tester->conn(), $config))->add($attributes);
        $after = $this->tester->grabNumRecords('migrations-load');
        $this->assertEquals($before + 1, $after);
    }

    public function testRemoveById()
    {
        $config = $this->make(Config::class, ['getMigrationTable' => 'migrations-remove-by-id']);
        $before = $this->tester->grabNumRecords('migrations-remove-by-id');
        (new MigrationRepository($this->tester->conn(), $config))->removeById(2);
        $after = $this->tester->grabNumRecords('migrations-remove-by-id');
        $this->assertEquals($before - 1, $after);
    }
}
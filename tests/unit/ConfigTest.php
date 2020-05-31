<?php

namespace kradwhite\tests\unit;

use kradwhite\migration\model\Config;
use kradwhite\migration\model\MigrationException;

class ConfigTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testConstructFail()
    {
        $this->tester->expectThrowable(new MigrationException('environment-not-found'), function () {
            new Config([]);
        });
    }

    public function testConstructSuccess()
    {
        new Config(['defaults' => ['environment' => 'testing']]);
    }

    public function testCreateSuccess()
    {
        $this->tester->amInPath('tests/_data/');
        $cwd = getcwd();
        if (file_exists($cwd . '/' . Config::Name)) {
            $this->tester->deleteFile(Config::Name);
        }
        (new Config(['defaults' => ['environment' => 'testing']]))->create($cwd . '/', 'ru');
        $this->tester->assertFileExists($cwd . '/' . Config::Name);
    }

    public function testGetEnvironmentFailNotFound()
    {
        $this->tester->expectThrowable(new MigrationException('config-file-environment-not-found', ['testing']), function () {
            (new Config(['defaults' => ['environment' => 'testing']]))->getEnvironment();
        });
    }

    public function testGetEnvironmentSuccess()
    {
        $config = ['defaults' => ['environment' => 'testing'], 'environments' => ['testing' => ['data']]];
        $environment = (new Config($config))->getEnvironment();
        $this->assertEquals($environment, ['data']);
    }

    public function testGetMigrationTableFail()
    {
        $this->tester->expectThrowable(new MigrationException('table-name-not-found'), function () {
            (new Config(['defaults' => ['environment' => 'testing']]))->getMigrationTable();
        });
    }

    public function testGetMigrationTableFromEnvironment()
    {
        $config = ['defaults' => ['environment' => 'testing'], 'environments' => ['testing' => ['table' => 'migrations']]];
        $tableName = (new Config($config))->getMigrationTable();
        $this->assertEquals($tableName, 'migrations');
    }

    public function testGetMigrationTableFromDefaults()
    {
        $config = ['defaults' => ['environment' => 'testing', 'table' => 'migrations'], 'environments' => []];
        $tableName = (new Config($config))->getMigrationTable();
        $this->assertEquals($tableName, 'migrations');
    }

    public function testGetPathFail()
    {
        $this->tester->expectThrowable(new MigrationException('migration-path-not-found'), function () {
            $config = ['defaults' => ['environment' => 'testing']];
            (new Config($config))->getPath();
        });
    }

    public function testGetPathSuccess()
    {
        $config = ['defaults' => ['environment' => 'testing'], 'paths' => ['migrations' => '_data']];
        $path = (new Config($config))->getPath();
        $this->assertEquals($path, getcwd() . '/_data');
    }

    public function testGetDriverFail()
    {
        $this->tester->expectThrowable(new MigrationException('driver-not-found', ['testing']), function () {
            $config = ['defaults' => ['environment' => 'testing']];
            (new Config($config))->getDriver();
        });
    }

    public function testGetDriverSuccess()
    {
        $config = ['defaults' => ['environment' => 'testing'], 'environments' => ['testing' => ['driver' => 'pgsql']]];
        $driver = (new Config($config))->getDriver();
        $this->assertEquals($driver, 'pgsql');
    }
}
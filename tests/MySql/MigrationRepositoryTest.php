<?php

namespace kradwhite\tests\MySql;

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

    public function testCreateFailAlreadyExists()
    {
        $this->tester->amInPath('tests/_data');
        $pwd = getcwd();
        if (!file_exists("$pwd/migration.php")) {
            $this->tester->writeToFile('migration.php', '<?php');
        }
        $this->tester->expectThrowable(new MigrationException('migration-already-exist', ["$pwd/migration.php"]), function () use ($pwd) {
            $config = $this->make(Config::class, ['getPath' => "$pwd"]);
            (new MigrationRepository($this->tester->conn(), $config))->createFile('migration', '');
        });
    }

    public function testCreateFailEmptyContent()
    {
        $this->tester->amInPath('tests/_data');
        $pwd = getcwd();
        if (file_exists("$pwd/migration.php")) {
            $this->tester->deleteFile('migration.php');
        }
        $this->tester->expectThrowable(new MigrationException('migration-file-create-error', ["$pwd/migration.php"]), function () use ($pwd) {
            $config = $this->make(Config::class, ['getPath' => "$pwd"]);
            (new MigrationRepository($this->tester->conn(), $config))->createFile('migration', '');
        });
    }

    public function testCreateSuccess()
    {
        $this->tester->amInPath('tests/_data');
        $pwd = getcwd();
        if (file_exists("$pwd/migration.php")) {
            $this->tester->deleteFile('migration.php');
        }
        $config = $this->make(Config::class, ['getPath' => "$pwd"]);
        (new MigrationRepository($this->tester->conn(), $config))->createFile('migration', '<?php');
    }

    public function testRemoveById()
    {
        $config = $this->make(Config::class, ['getMigrationTable' => 'migrations-remove-by-id']);
        $before = $this->tester->grabNumRecords('migrations-remove-by-id');
        (new MigrationRepository($this->tester->conn(), $config))->removeById(2);
        $after = $this->tester->grabNumRecords('migrations-remove-by-id');
        $this->assertEquals($before - 1, $after);
    }

    public function testBuildMigrationFailClassNotFound()
    {
        $this->tester->amInPath('tests/_data');
        $this->tester->expectThrowable(new MigrationException('migration-not-is-a-migration', ['not_extends_Migration']), function () {
            $pwd = getcwd();
            $config = $this->make(Config::class, ['getPath' => "$pwd"]);
            (new MigrationRepository($this->tester->conn(), $config))->buildMigration('not_extends_Migration');
        });
    }

    public function testBuildMigrationSuccess()
    {
        $this->tester->amInPath('tests/_data');
        $pwd = getcwd();
        $config = $this->make(Config::class, ['getPath' => "$pwd"]);
        $migration = (new MigrationRepository($this->tester->conn(), $config))->buildMigration('success_migration');
        $this->assertInstanceOf(Migration::class, $migration);
    }

    public function testLoadMigrationNamesFromDirectoryNotFound()
    {
        $pwd = getcwd();
        $this->tester->expectThrowable(new MigrationException('migration-dir-not-found', ["$pwd/wrong/path"]), function () use ($pwd) {
            $config = $this->make(Config::class, ['getPath' => "$pwd/wrong/path"]);
            (new MigrationRepository($this->tester->conn(), $config))->loadMigrationNamesFromDirectory();
        });
    }

    public function testLoadMigrationNamesFromDirectoryNotDirectory()
    {
        $this->tester->amInPath('tests/_data');
        $pwd = getcwd();
        $this->tester->expectThrowable(new MigrationException('migration-dir-not-dir', ["$pwd/not_directory.php"]), function () use ($pwd) {
            $config = $this->make(Config::class, ['getPath' => "$pwd/not_directory.php"]);
            (new MigrationRepository($this->tester->conn(), $config))->loadMigrationNamesFromDirectory();
        });
    }

    public function testLoadMigrationNamesFromDirectorySuccess()
    {
        $this->tester->amInPath('tests/_data');
        $config = $this->make(Config::class, ['getPath' => getcwd()]);
        $result = (new MigrationRepository($this->tester->conn(), $config))->loadMigrationNamesFromDirectory();
        $this->assertCount(2, $result);
        $this->assertEquals($result[0], '_2020_01_02_00_00_00__second_2');
        $this->assertEquals($result[1], '_2019_01_01__00_00_00__first_1');
    }
}
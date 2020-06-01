<?php
/**
 * Date: 11.04.2020
 * Time: 12:18
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use kradwhite\db\Connection;
use kradwhite\db\structure\Table;

/**
 * Class Migration
 * @package kradwhite\migration\model
 */
abstract class Migration
{
    /** @var Connection */
    private ?Connection $connection;

    /**
     * Migration constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return void
     */
    public function up()
    {

    }

    /**
     * @return void
     */
    public function down()
    {

    }

    /**
     * @param string $name
     * @param array $options
     * @return Table
     */
    protected function table(string $name, array $options): Table
    {
        return $this->connection->table($name, $options);
    }

    /**
     * @return Connection
     */
    protected function conn(): Connection
    {
        return $this->connection;
    }
}
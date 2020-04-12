<?php
/**
 * Date: 12.04.2020
 * Time: 18:34
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use kradwhite\db\Connection;

/**
 * Class MigrationRepository
 * @package model
 */
class MigrationRepository
{
    /** @var Connection */
    private ?Connection $connection = null;

    /** @var Config */
    private ?Config $config = null;

    /**
     * MigrationRepository constructor.
     * @param Connection|null $connection
     * @param Config|null $config
     */
    public function __construct(?Connection $connection, ?Config $config)
    {
        $this->connection = $connection;
        $this->config = $config;
    }


}
<?php
/**
 * Date: 12.04.2020
 * Time: 18:32
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

/**
 * Class Migrations
 * @package kradwhite\migration\model
 */
class Migrations
{
    /** @var array */
    private array $migrations = [];

    /** @var Config */
    private ?Config $config = null;

    /**
     * Migrations constructor.
     * @param array $migrations
     * @param Config|null $config
     */
    public function __construct(array $migrations, ?Config $config)
    {
        $this->migrations = $migrations;
        $this->config = $config;
    }


}
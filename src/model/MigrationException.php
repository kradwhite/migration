<?php
/**
 * Date: 12.04.2020
 * Time: 17:48
 * Author: Artem Aleksandrov
 */

declare (strict_types=1);

namespace kradwhite\migration\model;

use kradwhite\language\LangException;
use Throwable;

/**
 * Class MigrationException
 * @package model
 */
class MigrationException extends \Exception
{
    /**
     * MigrationException constructor.
     * @param $id
     * @param array $params
     * @param Throwable|null $previous
     * @throws LangException
     */
    public function __construct($id, array $params = [], ?Throwable $previous = null)
    {
        $code = $previous ? $previous->getCode() : 0;
        parent::__construct(App::lang()->phrase('exceptions', $id, $params), $code, $previous);
    }
}
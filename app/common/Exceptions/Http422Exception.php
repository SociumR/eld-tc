<?php

namespace App\Common\Exceptions;


use App\Services\ServiceException;
use Phalcon\Exception;

class Http422Exception extends Exception
{

    /**
     * Http422Exception constructor.
     * @param string $getMessage
     * @param int|mixed $getCode
     * @param ServiceException|\Exception $e
     */
    public function __construct($getMessage, $getCode, $e)
    {
    }
}
<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Exception;

use Exception;

class ApiLoginFailedException extends Exception
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = 'Api Login Failed', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}

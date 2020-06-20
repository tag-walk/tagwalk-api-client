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
    public function __construct($message = 'Api Login Failed', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}

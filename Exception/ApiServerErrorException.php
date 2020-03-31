<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Steve Valette <steve@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Exception;

use Exception;

class ApiServerErrorException extends Exception
{
    public function __construct($message = 'Api Server Error')
    {
        parent::__construct($message);
    }
}

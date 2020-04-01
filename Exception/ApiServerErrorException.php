<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
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

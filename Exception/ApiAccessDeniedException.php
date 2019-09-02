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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiAccessDeniedException extends AccessDeniedHttpException
{
}

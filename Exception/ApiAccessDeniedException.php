<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Steve Valette <steve@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Exception;

use Exception;

class ApiAccessDeniedException extends Exception
{
    private const DEFAULT_MESSAGE = 'Api Access Denied';

    /**
     * {@inheritDoc}
     */
    public function __construct(
        string $message = self::DEFAULT_MESSAGE,
        ?int $code = 403,
        Exception $previous = null

    ) {
        parent::__construct($message, $code, $previous);
    }
}
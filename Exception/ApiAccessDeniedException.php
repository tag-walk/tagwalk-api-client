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
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiAccessDeniedException extends HttpException
{
    private const DEFAULT_MESSAGE = 'Access Denied moodboard';

    /**
     * {@inheritDoc}
     */
    public function __construct(
        int $statusCode = 403,
        string $message = self::DEFAULT_MESSAGE,
        Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
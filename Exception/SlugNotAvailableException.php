<?php

namespace Tagwalk\ApiClientBundle\Exception;

class SlugNotAvailableException extends \Exception
{
    private static string $defaultMessage = 'Cannot create the document cause one with the same slug already exists.';

    public function __construct($message = "")
    {
        parent::__construct($message ?: self::$defaultMessage);
    }
}

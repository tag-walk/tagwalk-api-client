<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Utils\Constants;

use ReflectionClass;
use ReflectionException;

/**
 * Class Constants.
 */
abstract class Constants
{
    /**
     * Return the list of all declared constants.
     *
     * @return array
     */
    public static function getAllowedValues(): array
    {
        try {
            $oClass = new ReflectionClass(static::class);
            $constants = $oClass->getConstants();
            foreach ($constants as $key => $constant) {
                if (false === is_string($constant)) {
                    unset($constants[$key]);
                }
            }

            return $constants;
        } catch (ReflectionException $reflectionException) {
            return [];
        }
    }

    /**
     * Return an associated array of all constants (keys and values are identicals).
     *
     * @return array
     */
    public static function getOptions(): array
    {
        try {
            $oClass = new ReflectionClass(static::class);
            $constants = $oClass->getConstants();
            foreach ($constants as $key => $constant) {
                if (false === is_string($constant)) {
                    unset($constants[$key]);
                }
            }
            $constants = array_combine(array_values($constants), array_values($constants));

            return $constants;
        } catch (ReflectionException $reflectionException) {
            return [];
        }
    }

    /**
     * Return an associated array of all constants (keys and values are identicals).
     *
     * @param string $prefix
     *
     * @return array
     */
    public static function getPrefixedOptions(string $prefix): array
    {
        try {
            $oClass = new ReflectionClass(static::class);
            $constants = $oClass->getConstants();
            foreach ($constants as $key => $constant) {
                if (false === is_string($constant)) {
                    unset($constants[$key]);
                }
            }
            $constants = array_combine(substr_replace(array_values($constants), $prefix, 0, 0), array_values($constants));

            return $constants;
        } catch (ReflectionException $reflectionException) {
            return [];
        }
    }
}

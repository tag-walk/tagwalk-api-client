<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Utils\Constants
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Utils\Constants;

/**
 * Class Constants
 *
 * @package Tagwalk\ApiClientBundle\Utils\Constants
 */
abstract class Constants
{
    /**
     * Return the list of all declared constants
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getAllowedValues()
    {
        $oClass = new \ReflectionClass(get_called_class());
        $constants = $oClass->getConstants();
        foreach ($constants as $key => $constant) {
            if (false === is_string($constant)) {
                unset($constants[$key]);
            }
        }

        return $constants;
    }

    /**
     * Return an associated array of all constants (keys and values are identicals)
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getOptions()
    {
        $oClass = new \ReflectionClass(get_called_class());
        $constants = $oClass->getConstants();
        foreach ($constants as $key => $constant) {
            if (false === is_string($constant)) {
                unset($constants[$key]);
            }
        }
        $constants = array_combine(array_values($constants), array_values($constants));

        return $constants;
    }
}

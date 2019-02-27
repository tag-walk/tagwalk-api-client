<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Utils;

use Tagwalk\ApiClientBundle\Model\Traits\Positionable;

class Reindexer
{
    /**
     * Reorder collection items by position
     *
     * @param Positionable[] $collection
     */
    public static function reindex(?array &$collection): void
    {
        if (false === empty($collection)) {
            usort($collection, function ($a, $b) {
                /** @var Positionable $a */
                /** @var Positionable $b */
                if ($a->getPosition() == $b->getPosition()) {
                    return 0;
                }

                return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
            });
            for ($index = 0, $count = count($collection); $index < $count; $index++) {
                $collection[$index]->setPosition($index);
            }
        } else {
            $collection = [];
        }
    }
}

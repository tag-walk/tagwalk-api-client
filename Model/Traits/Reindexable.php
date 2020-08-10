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

namespace Tagwalk\ApiClientBundle\Model\Traits;

/**
 * Trait Reindexable.
 *
 * Allow to reindex nested positionable items
 */
trait Reindexable
{
    /**
     * Reorder collection items by position.
     *
     * @param array $collection
     *
     * @return array
     */
    protected function reindex(?array $collection)
    {
        if (false === empty($collection)) {
            /** @var Positionable[] $collection */
            usort($collection, static function ($a, $b) {
                return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
            });
            foreach ($collection as $i => $item) {
                $item->setPosition($i);
            }
        }

        return $collection;
    }
}

<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Document
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;

/**
 * Describe City document
 *
 * @see Document
 */
class City extends AbstractDocument
{
    use NameTranslatable;

    /**
     * @var bool
     * @Assert\Type("boolean")
     * @SWG\Property(
     *     description="City belongs to the main cities or to the rest of the world",
     *     default=true
     * )
     */
    private $main = true;

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->main;
    }

    /**
     * @param bool $main
     *
     * @return self
     */
    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }
}

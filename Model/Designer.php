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

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Notable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Watermarkable;
use Symfony\Component\Validator\Constraints as Assert;

class Designer
{
    use Descriptable;
    use NameTranslatable;
    use Notable;
    use Positionable;
    use Watermarkable;

    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $talent = false;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $details;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $detailsFr;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $detailsEs;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $detailsIt;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $detailsZh;

    /**
     * @return bool|null
     */
    public function getTalent(): ?bool
    {
        return $this->talent;
    }

    /**
     * @param bool $talent
     */
    public function setTalent(bool $talent)
    {
        $this->talent = $talent;
    }

    /**
     * @return null|string
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @param string $details
     */
    public function setDetails(string $details)
    {
        $this->details = $details;
    }

    /**
     * @return null|string
     */
    public function getDetailsFr(): ?string
    {
        return $this->detailsFr;
    }

    /**
     * @param string $detailsFr
     */
    public function setDetailsFr(string $detailsFr)
    {
        $this->detailsFr = $detailsFr;
    }

    /**
     * @return null|string
     */
    public function getDetailsEs(): ?string
    {
        return $this->detailsEs;
    }

    /**
     * @param string $detailsEs
     */
    public function setDetailsEs(string $detailsEs)
    {
        $this->detailsEs = $detailsEs;
    }

    /**
     * @return null|string
     */
    public function getDetailsIt(): ?string
    {
        return $this->detailsIt;
    }

    /**
     * @param string $detailsIt
     */
    public function setDetailsIt(string $detailsIt)
    {
        $this->detailsIt = $detailsIt;
    }

    /**
     * @return null|string
     */
    public function getDetailsZh(): ?string
    {
        return $this->detailsZh;
    }

    /**
     * @param string $detailsZh
     */
    public function setDetailsZh(string $detailsZh)
    {
        $this->detailsZh = $detailsZh;
    }
}

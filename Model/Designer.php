<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Document
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Notable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Watermarkable;

class Designer extends AbstractDocument
{
    use Coverable;
    use Descriptable;
    use NameTranslatable;
    use Notable;
    use Positionable;
    use Watermarkable;

    /**
     * @var bool
     */
    private $talent = false;

    /**
     * @var string|null
     */
    private $details = null;

    /**
     * @var string|null
     */
    private $detailsEs;

    /**
     * @var string|null
     */
    private $detailsFr;

    /**
     * @var string|null
     */
    private $detailsIt;

    /**
     * @var string|null
     */
    private $detailsZh;

    /**
     * @return bool
     */
    public function getTalent(): bool
    {
        return $this->talent;
    }

    /**
     * @param bool $talent
     *
     * @return self
     */
    public function setTalent(bool $talent): self
    {
        $this->talent = $talent;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @param null|string $details
     *
     * @return self
     */
    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDetailsEs(): ?string
    {
        return $this->detailsEs;
    }

    /**
     * @param null|string $detailsEs
     *
     * @return self
     */
    public function setDetailsEs(?string $detailsEs): self
    {
        $this->detailsEs = $detailsEs;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDetailsFr(): ?string
    {
        return $this->detailsFr;
    }

    /**
     * @param null|string $detailsFr
     *
     * @return self
     */
    public function setDetailsFr(?string $detailsFr): self
    {
        $this->detailsFr = $detailsFr;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDetailsIt(): ?string
    {
        return $this->detailsIt;
    }

    /**
     * @param null|string $detailsIt
     *
     * @return self
     */
    public function setDetailsIt(?string $detailsIt): self
    {
        $this->detailsIt = $detailsIt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDetailsZh(): ?string
    {
        return $this->detailsZh;
    }

    /**
     * @param null|string $detailsZh
     *
     * @return self
     */
    public function setDetailsZh(?string $detailsZh): self
    {
        $this->detailsZh = $detailsZh;

        return $this;
    }
}

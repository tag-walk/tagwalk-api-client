<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Document
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Notable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\SlugTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Suggestable;

class Tag extends AbstractDocument
{
    use SlugTranslatable;
    use NameTranslatable;
    use Notable;
    use Positionable;
    use Suggestable;

    /**
     * @var Tag[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $similars;

    /**
     * @var boolean
     * @Assert\Type("boolean")
     */
    private $beauty = false;

    /**
     * @var string[]|null
     */
    private $synonyms;

    /**
     * @var string[]|null
     */
    private $synonymsFr;

    /**
     * @var string[]|null
     */
    private $synonymsEs;

    /**
     * @var string[]|null
     */
    private $synonymsIt;

    /**
     * @var string[]|null
     */
    private $synonymsZh;

    /**
     * @return Tag[]|null
     */
    public function getSimilars(): ?array
    {
        return $this->similars;
    }

    /**
     * @param Tag[]|null $similars
     *
     * @return self
     */
    public function setSimilars(?array $similars): self
    {
        $this->similars = $similars;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBeauty(): bool
    {
        return $this->beauty;
    }


    /**
     * @param bool $beauty
     * @return self
     */
    public function setBeauty(bool $beauty): self
    {
        $this->beauty = $beauty;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getSynonyms(): ?array
    {
        return $this->synonyms;
    }

    /**
     * @param null|string[] $synonyms
     *
     * @return self
     */
    public function setSynonyms(?array $synonyms): self
    {
        $this->synonyms = $synonyms;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getSynonymsFr(): ?array
    {
        return $this->synonymsFr;
    }

    /**
     * @param null|string[] $synonymsFr
     *
     * @return self
     */
    public function setSynonymsFr(?array $synonymsFr): self
    {
        $this->synonymsFr = $synonymsFr;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getSynonymsEs(): ?array
    {
        return $this->synonymsEs;
    }

    /**
     * @param null|string[] $synonymsEs
     *
     * @return self
     */
    public function setSynonymsEs(?array $synonymsEs): self
    {
        $this->synonymsEs = $synonymsEs;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getSynonymsIt(): ?array
    {
        return $this->synonymsIt;
    }

    /**
     * @param null|string[] $synonymsIt
     *
     * @return self
     */
    public function setSynonymsIt(?array $synonymsIt): self
    {
        $this->synonymsIt = $synonymsIt;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getSynonymsZh(): ?array
    {
        return $this->synonymsZh;
    }

    /**
     * @param null|string[] $synonymsZh
     *
     * @return self
     */
    public function setSynonymsZh(?array $synonymsZh): self
    {
        $this->synonymsZh = $synonymsZh;

        return $this;
    }
}

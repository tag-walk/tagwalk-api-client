<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;
use Tagwalk\ApiClientBundle\Model\Traits\Fileable;
use Tagwalk\ApiClientBundle\Model\Traits\Linkable;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;

class News extends AbstractDocument
{
    use NameTranslatable;
    use Descriptable;
    use Textable;
    use Linkable;
    use Fileable;
    use Coverable;

    /**
     * @var \DateTimeInterface|null
     * @Assert\DateTime()
     */
    protected $date;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $text;

    /**
     * @var string[]|null
     * @Assert\Type("array")
     * @Assert\Choice(
     *     callback={"App\Utils\Constants\NewsCategories", "getAllowedValues"},
     *     multiple=true
     * )
     */
    private $categories;

    /**
     * @var array
     */
    private $categoriesI18n;

    /**
     * @var Designer[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $designers;

    /**
     * @var Individual[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $individuals;

    /**
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     *
     * @return self
     */
    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param null|string[] $categories
     *
     * @return self
     */
    public function setCategories(?array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return array
     */
    public function getCategoriesI18n()
    {
        return $this->categoriesI18n;
    }

    /**
     * @param array $categoriesI18n
     *
     * @return News
     */
    public function setCategoriesI18n($categoriesI18n): self
    {
        $this->categoriesI18n = $categoriesI18n;

        return $this;
    }

    /**
     * @return null|Designer[]
     */
    public function getDesigners(): ?array
    {
        return $this->designers;
    }

    /**
     * @param null|Designer[] $designers
     *
     * @return self
     */
    public function setDesigners(?array $designers): self
    {
        $this->designers = $designers;

        return $this;
    }

    /**
     * @return null|Individual[]
     */
    public function getIndividuals(): ?array
    {
        return $this->individuals;
    }

    /**
     * @param null|Individual[] $individuals
     *
     * @return self
     */
    public function setIndividuals(?array $individuals): self
    {
        $this->individuals = $individuals;

        return $this;
    }
}

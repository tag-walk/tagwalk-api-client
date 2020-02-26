<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Document
 * @author      Vincent Duruflé <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;
use Tagwalk\ApiClientBundle\Model\Traits\Fileable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tag Talk Document
 */
class Talk extends AbstractDocument
{
    use Descriptable;
    use Textable;
    use Fileable;
    use Coverable;
    use Positionable;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 255
     * )
     */
    protected $author;

    /**
     * @var Designer|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $designer;

    /**
     * @var Individual|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $individual;

    /**
     * @var Tag[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $tags;

    /**
     * @var Season|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $season;

    /**
     * @var string|null
     * @Assert\Choice(callback={"App\Utils\Constants\MediaType", "getAllowedValues"})
     */
    private $type = 'woman';

    /**
     * @var string[]|null
     * @Assert\Type("array")
     * @Assert\Choice(
     *     callback={"App\Utils\Constants\TalkCategories", "getAllowedValues"},
     *     multiple=true
     * )
     */
    private $categories;

    /**
     * @var \DateTimeInterface|null
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var Resource[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $resources;

    /**
     * @return Resource[]|null
     */
    public function getResources(): ?array
    {
        return $this->resources;
    }

    /**
     * @param Resource[]|null $resources
     *
     * @return self
     */
    public function setResources(?array $resources): self
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param null|string $author
     *
     * @return self
     */
    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Designer|null
     */
    public function getDesigner(): ?Designer
    {
        return $this->designer;
    }

    /**
     * @param Designer|null $designer
     *
     * @return self
     */
    public function setDesigner(?Designer $designer): self
    {
        $this->designer = $designer;

        return $this;
    }

    /**
     * @return Individual|null
     */
    public function getIndividual(): ?Individual
    {
        return $this->individual;
    }

    /**
     * @param Individual|null $individual
     *
     * @return self
     */
    public function setIndividual(?Individual $individual): self
    {
        $this->individual = $individual;

        return $this;
    }

    /**
     * @return Tag[]|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param Tag[]|null $tags
     *
     * @return self
     */
    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return Season|null
     */
    public function getSeason(): ?Season
    {
        return $this->season;
    }

    /**
     * @param Season|null $season
     *
     * @return self
     */
    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

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
}

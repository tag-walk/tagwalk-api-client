<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author Vincent Duruflé <vincent@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;
use Tagwalk\ApiClientBundle\Utils\Constants\MediaType;
use Tagwalk\ApiClientBundle\Utils\Reindexer;
use Symfony\Component\Validator\Constraints as Assert;

class Trend extends AbstractDocument
{
    use Textable;
    use Coverable;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 255
     * )
     */
    protected $title;

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
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Choice(callback={"App\Utils\Constants\MediaType", "getAllowedValues"})
     */
    protected $type = MediaType::WOMENSWEAR;

    /**
     * @var Season
     * @Assert\Type("object")
     * @Assert\NotBlank()
     */
    protected $season;

    /**
     * @var City|null
     * @Assert\Type("object")
     */
    protected $city;

    /**
     * @var TagMedia[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    protected $tags;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Season
     */
    public function getSeason(): Season
    {
        return $this->season;
    }

    /**
     * @param Season $season
     *
     * @return self
     */
    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     *
     * @return self
     */
    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return TagMedia[]|null
     */
    public function getTags(): ?array
    {
        if (null === $this->tags) {
            $this->tags = [];
        }

        return $this->tags;
    }

    /**
     * @param TagMedia[]|null $tags
     *
     * @return self
     */
    public function setTags(?array $tags): self
    {
        if (null === $tags) {
            $tags = [];
        }
        $this->tags = $tags;

        return $this;
    }

    /**
     * @param TagMedia $tag
     *
     * @return Trend
     */
    public function addTag(TagMedia $tag): self
    {
        if (null === $this->tags) {
            $this->tags = [];
        }
        $this->tags[] = $tag;
        Reindexer::reindex($this->tags);

        return $this;
    }

    /**
     * @param string $slug
     *
     * @return Trend
     */
    public function removeTag(string $slug): self
    {
        foreach ($this->tags as $i => $tag) {
            if ($tag->getSlug() === $slug) {
                unset($this->tags[$i]);
            }
        }
        Reindexer::reindex($this->tags);

        return $this;
    }
}

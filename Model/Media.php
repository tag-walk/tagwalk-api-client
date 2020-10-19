<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Watermarkable;
use Tagwalk\ApiClientBundle\Utils\Constants\AccessoryCategories;
use Tagwalk\ApiClientBundle\Utils\Constants\MediaType;
use Tagwalk\ApiClientBundle\Utils\Reindexer;

class Media extends AbstractDocument
{
    use Positionable;
    use Watermarkable;
    use Descriptable;

    /**
     * @var File[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $files;

    /**
     * @var Individual[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $individuals;

    /**
     * @var Tag[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $tags;

    /**
     * @var Affiliation[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $affiliations;

    /**
     * @var Season|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $season;

    /**
     * @var City|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $city;

    /**
     * @var Designer|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $designer;

    /**
     * @var Member[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $members;

    /**
     * @var string
     * @Assert\Type("string")
     */
    private $type = MediaType::WOMENSWEAR;

    /**
     * @var int|null
     * @Assert\Type("int")
     */
    private $look;

    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $lookbook = false;

    /**
     * @var string[]|null
     */
    private $accessoryCategories;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $reference;

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $downloadable;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $category;

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $isWatermarked;

    /**
     * @return null|Season
     */
    public function getSeason(): ?Season
    {
        return $this->season;
    }

    /**
     * @param null|Season $season
     */
    public function setSeason(?Season $season)
    {
        $this->season = $season;
    }

    /**
     * @return null|City
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param null|City $city
     */
    public function setCity(?City $city)
    {
        $this->city = $city;
    }

    /**
     * @return null|Designer
     */
    public function getDesigner(): ?Designer
    {
        return $this->designer;
    }

    /**
     * @param null|Designer $designer
     */
    public function setDesigner(?Designer $designer)
    {
        $this->designer = $designer;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType(?string $type)
    {
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getLook(): ?int
    {
        return $this->look;
    }

    /**
     * @param int|null $look
     */
    public function setLook(?int $look)
    {
        $this->look = $look;
    }

    /**
     * @return bool|null
     */
    public function getLookbook(): ?bool
    {
        return $this->lookbook;
    }

    /**
     * @param bool|null $lookbook
     */
    public function setLookbook(?bool $lookbook)
    {
        $this->lookbook = $lookbook;
    }

    /**
     * @return File[]|null
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param File[]|null $files
     */
    public function setFiles(?array $files)
    {
        if (null === $files) {
            $files = [];
        }
        $this->files = $files;
        Reindexer::reindex($this->files);
    }

    /**
     * @param File $file
     */
    public function addFile(File $file)
    {
        if (null === $this->files) {
            $this->files = [];
        }
        $this->files[] = $file;
        Reindexer::reindex($this->files);
    }

    /**
     * @param string $slug
     */
    public function removeFile(string $slug)
    {
        foreach ($this->files as $i => $file) {
            if ($file->getFilename() === $slug) {
                unset($this->files[$i]);
            }
        }
        Reindexer::reindex($this->files);
    }

    /**
     * @return Affiliation[]|null
     */
    public function getAffiliations(): ?array
    {
        return $this->affiliations;
    }

    /**
     * @param Affiliation[]|null $affiliations
     */
    public function setAffiliations(?array $affiliations = [])
    {
        if (null === $affiliations) {
            $affiliations = [];
        }
        $this->affiliations = $affiliations;
        Reindexer::reindex($this->affiliations);
    }

    /**
     * @param Affiliation $affiliation
     */
    public function addAffiliation(Affiliation $affiliation)
    {
        if (null === $this->affiliations) {
            $this->affiliations = [];
        }
        $this->affiliations[] = $affiliation;
        Reindexer::reindex($this->affiliations);
    }

    /**
     * @param string $affiliationSlug
     */
    public function removeAffiliation(string $affiliationSlug)
    {
        foreach ($this->affiliations as $i => $affiliation) {
            if ($affiliation->getSlug() === $affiliationSlug) {
                unset($this->affiliations[$i]);
            }
        }
        Reindexer::reindex($this->affiliations);
    }

    /**
     * @return Tag[]|null
     */
    public function getTags(): ?array
    {
        if (null === $this->tags) {
            $this->tags = [];
        }

        return $this->tags;
    }

    /**
     * @param Tag[]|null $tags
     */
    public function setTags(?array $tags)
    {
        if (null === $tags) {
            $tags = [];
        }
        $this->tags = $tags;
    }

    /**
     * @param Tag|null $tag
     */
    public function addTag(?Tag $tag)
    {
        if (null !== $tag) {
            if (empty($this->tags)) {
                $this->tags = [$tag];
            } else {
                $this->tags[] = $tag;
            }
        }
    }

    /**
     * @param string $slug
     */
    public function removeTag(string $slug)
    {
        foreach ($this->tags as $i => $tag) {
            if ($tag->getSlug() === $slug) {
                unset($this->tags[$i]);
            }
        }
    }

    /**
     * @return Individual[]|null
     */
    public function getIndividuals(): ?array
    {
        return $this->individuals;
    }

    /**
     * @param Individual[] $individuals
     */
    public function setIndividuals(?array $individuals)
    {
        $this->individuals = $individuals;
    }

    /**
     * @return string[]|null
     */
    public function getAccessoryCategories(): ?array
    {
        return $this->accessoryCategories;
    }

    /**
     * @param string[]|null $accessoryCategories
     */
    public function setAccessoryCategories(?array $accessoryCategories)
    {
        $this->accessoryCategories = $accessoryCategories;
    }

    /**
     * @return Member[]|null
     */
    public function getMembers(): ?array
    {
        if (null === $this->members) {
            $this->members = [];
        }

        return $this->members;
    }

    /**
     * @param Member[]|null $members
     *
     * @return self
     */
    public function setMembers(?array $members): self
    {
        if (null === $members) {
            $members = [];
        }
        $this->members = $members;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     *
     * @return self
     */
    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isDownloadable(): ?bool
    {
        return $this->downloadable;
    }

    /**
     * @param bool|null $downloadable
     *
     * @return self
     */
    public function setDownloadable(?bool $downloadable): self
    {
        $this->downloadable = $downloadable;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string|null $category
     *
     * @return self
     */
    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsWatermarked(): ?bool
    {
        return $this->isWatermarked;
    }

    /**
     * @param bool|null $isWatermarked
     *
     * @return self
     */
    public function setIsWatermarked(?bool $isWatermarked): self
    {
        $this->isWatermarked = $isWatermarked;

        return $this;
    }
}

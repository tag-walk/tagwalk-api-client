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
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Watermarkable;
use Tagwalk\ApiClientBundle\Utils\Reindexer;

class Streetstyle extends AbstractDocument
{
    use Positionable;
    use Watermarkable;

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
     * @var Designer[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $designers;

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
     * @return Designer[]|null
     */
    public function getDesigners(): ?array
    {
        return $this->designers;
    }

    /**
     * @param Designer[]|null $designers
     */
    public function setDesigners(?array $designers)
    {
        $this->designers = $designers;
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
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
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
     * @return Individual[]
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
     * @return File[]|null
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param File[] $files
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
            if ($file->getSlug() === $slug) {
                unset($this->files[$i]);
            }
        }
        Reindexer::reindex($this->files);
    }

    /**
     * @return Affiliation[]
     */
    public function getAffiliations(): ?array
    {
        return $this->affiliations;
    }

    /**
     * @param Affiliation[] $affiliations
     */
    public function setAffiliations(?array $affiliations)
    {
        if (null === $affiliations) {
            $affiliations = [];
        }
        Reindexer::reindex($affiliations);
        $this->affiliations = $affiliations;
    }

    /**
     * @param Affiliation $affiliation
     */
    public function addAffiliations(Affiliation $affiliation)
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
}

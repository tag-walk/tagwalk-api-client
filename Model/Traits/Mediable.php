<?php

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Utils\Reindexer;

/**
 * Trait Mediable.
 *
 * Add medias property to a document
 */
trait Mediable
{
    /**
     * @var Media[]|null
     * @Assert\Valid()
     */
    protected $medias;

    /**
     * Get the medias collection.
     *
     * @return Media[]|null
     */
    public function getMedias(): ?array
    {
        if (null === $this->medias) {
            $this->medias = [];
        }

        return $this->medias;
    }

    /**
     * Set the media collection.
     *
     * @param Media[]|null $medias
     *
     * @return self
     */
    public function setMedias(?array $medias): self
    {
        if (null === $medias) {
            $medias = [];
        }
        $this->medias = $medias;
        Reindexer::reindex($this->medias);

        return $this;
    }

    /**
     * Add an element to the media collection.
     *
     * @param Media $media
     *
     * @return self
     */
    public function addMedia(Media $media): self
    {
        if (null === $this->medias) {
            $this->medias = [];
        }
        if (null === $media->getPosition()) {
            $media->setPosition(count($this->medias) + 1);
        }
        $this->medias[] = $media;
        Reindexer::reindex($this->medias);

        return $this;
    }

    /**
     * Remove an element from the media collection.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function removeMedia(string $slug): bool
    {
        foreach ($this->medias as $key => $media) {
            if ($slug === $media->getSlug()) {
                unset($this->medias[$key]);

                return true;
            }
        }
        Reindexer::reindex($this->medias);

        return false;
    }
}

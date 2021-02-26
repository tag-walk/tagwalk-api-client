<?php

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Utils\Reindexer;

class WornLook extends AbstractDocument
{
    /**
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private ?Individual $individual = null;

    /**
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private ?Event $event = null;

    /**
     * @Assert\NotBlank()
     */
    private string $lookSlug;

    /**
     * @var File[]
     *
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private array $files = [];

    public function getIndividual(): ?Individual
    {
        return $this->individual;
    }

    public function setIndividual(Individual $individual): self
    {
        $this->individual = $individual;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getLookSlug(): string
    {
        return $this->lookSlug;
    }

    public function setLookSlug(string $lookSlug): self
    {
        $this->lookSlug = $lookSlug;

        return $this;
    }

    /** @return File[] */
    public function getFiles(): array
    {
        return $this->files;
    }

    /** @param File[] $files */
    public function setFiles(array $files): self
    {
        $this->files = $files;
        Reindexer::reindex($this->files);

        return $this;
    }

    public function addFile(File $file): self
    {
        $this->files[] = $file;
        Reindexer::reindex($this->files);

        return $this;
    }

    public function removeFile(string $slug): self
    {
        foreach ($this->files as $i => $file) {
            if ($file->getSlug() === $slug) {
                unset($this->files[$i]);
            }
        }

        Reindexer::reindex($this->files);

        return $this;
    }
}

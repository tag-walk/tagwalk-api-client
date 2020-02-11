<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\File;

/**
 * Add files property to document.
 */
trait Fileable
{
    /* Used to reindex files collection on set/add/remove */
    use Reindexable;

    /**
     * @var File[]|null
     * @Assert\Valid()
     */
    private $files;

    /**
     * @return File[]|null
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param File[]|null $files
     *
     * @return self
     */
    public function setFiles(?array $files): self
    {
        $this->files = $this->reindex($files);

        return $this;
    }

    /**
     * @param File $file
     *
     * @return self
     */
    public function addFile(File $file): self
    {
        if (null === $this->files) {
            $this->files = [];
        }
        $conflict = null;
        $this->reindex($this->files);
        foreach ($this->files as $item) {
            if ($item->getPosition() === $file->getPosition()) {
                $conflict = $item;
            }
            if ($conflict) {
                $item->setPosition($item->getPosition() + 1);
            }
        }
        $this->files[] = $file;
        $this->reindex($this->files);

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return self
     */
    public function removeFile(string $filename): self
    {
        foreach ($this->files as $i => $file) {
            if ($file->getFilename() === $filename) {
                unset($this->files[$i]);
            }
        }
        $this->reindex($this->files);

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function hasFile(string $filename): bool
    {
        return !empty(array_filter($this->files, static function (File $file) use ($filename) {
            return $file->getFilename() === $filename;
        }));
    }
}

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

namespace Tagwalk\ApiClientBundle\Model;

/**
 * Interface Document.
 *
 * Implemented by elasticsearch entities
 *
 * @see AbstractDocument
 */
interface Document
{
    /**
     * @return string|null
     */
    public function getSlug(): ?string;

    /**
     * @param string|null $slug
     *
     * @return Document
     */
    public function setSlug(?string $slug);

    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     *
     * @return Document
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     *
     * @return Document
     */
    public function setStatus(string $status);

    /**
     * @return Document
     */
    public function enable();

    /**
     * @return Document
     */
    public function disable();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $createdAt
     *
     * @return Document
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt);

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $updatedAt
     *
     * @return Document
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt);
}

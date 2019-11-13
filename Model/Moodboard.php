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

class Moodboard extends AbstractDocument
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max=255, groups={"Moodboard"})
     */
    protected $name;

    /**
     * @var Media[]|null
     */
    private $medias;

    /**
     * @var Streetstyle[]|null
     */
    private $streetstyles;

    /**
     * @var User|null
     * @Assert\Type("object")
     */
    private $user;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $token;

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token)
    {
        $this->token = $token;
    }

    /**
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
     * @param array|null $medias
     */
    public function setMedias(?array $medias)
    {
        if (null === $medias) {
            $medias = [];
        }
        $this->medias = $medias;
    }

    /**
     * @param Media $media
     */
    public function addMedia(Media $media)
    {
        if (null === $this->medias) {
            $this->medias = [];
        }
        if (null === $this->streetstyles) {
            $this->streetstyles = [];
        }
        if (null === $media->getPosition()) {
            $countItems = count($this->medias) + count($this->streetstyles);
            $media->setPosition($countItems);
        }
        $this->medias[] = $media;
    }

    /**
     * @param string $slug
     */
    public function removeMedia(string $slug)
    {
        foreach ($this->medias as $key => $media) {
            if ($slug === $media->getSlug()) {
                unset($this->medias[$key]);
            }
        }
    }

    /**
     * @return Streetstyle[]|null
     */
    public function getStreetstyles(): ?array
    {
        if (null === $this->streetstyles) {
            $this->streetstyles = [];
        }

        return $this->streetstyles;
    }

    /**
     * @param Streetstyle[]|null $streetstyles
     */
    public function setStreetstyles(?array $streetstyles)
    {
        if (null === $streetstyles) {
            $streetstyles = [];
        }
        $this->streetstyles = $streetstyles;
    }

    /**
     * @param Streetstyle $streetstyle
     */
    public function addStreetstyle(Streetstyle $streetstyle)
    {
        if (null === $this->streetstyles) {
            $this->streetstyles = [];
        }
        if (null === $this->medias) {
            $this->medias = [];
        }
        if (null === $streetstyle->getPosition()) {
            $countItems = count($this->medias) + count($this->streetstyles);
            $streetstyle->setPosition($countItems);
        }
        $this->streetstyles[] = $streetstyle;
    }

    /**
     * @param string $slug
     */
    public function removeStreetstyle(string $slug)
    {
        foreach ($this->streetstyles as $key => $streetstyle) {
            if ($slug === $streetstyle->getSlug()) {
                unset($this->streetstyles[$key]);
            }
        }
    }
}

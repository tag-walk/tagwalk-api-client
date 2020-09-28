<?php

/** @noinspection TraitsPropertiesConflictsInspection */

/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Describe User document.
 *
 * Used for persistance and authentication
 *
 * @see Document
 * @see UserInterface
 */
class User extends AbstractDocument implements UserInterface, EquatableInterface
{
    /**
     * Override Sluggable property definition to reset regex assert
     *
     * @var string
     */
    protected $slug;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $firstname;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $lastname;

    /**
     * @var string
     * @Assert\Email(groups={"Default", "ShowroomUser", "base"})
     */
    private $email;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $gender;

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $fashionIndustry;

    /**
     * @var string|null
     * @Assert\Type("string", groups={"ShowroomUser"})
     */
    private $jobTitle;

    /**
     * @var bool|null
     * @Assert\Type("bool", groups={"ShowroomUser"})
     */
    private $newsletter;

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $survey;

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $vip = false;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $sector;

    /**
     * @var string|null
     * @Assert\NotBlank(groups={"Default"})
     */
    private $country;

    /**
     * @var string|null
     * @Assert\NotBlank(groups={"ShowroomUser"})
     */
    private $locale;

    /**
     * @var string|null
     * @Assert\Type("string", groups={"ShowroomUser"})
     */
    private $salt;

    /**
     * @var string|null
     * @Assert\Type("string", groups={"ShowroomUser"})
     */
    private $password;

    /**
     * @var string[]|null
     */
    private $roles;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $facebookId;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $token;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $apiToken;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $company;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $note;

    /**
     * @var string|null DateInterval format
     */
    private $duration;

    /**
     * @var \DateTime|null
     */
    private $expiresAt;

    /**
     * @param string|null $name
     * @param string|null $password
     * @param string|null $salt
     * @param array|null  $roles
     */
    public function __construct(
        ?string $name = null,
        ?string $password = null,
        ?string $salt = null,
        ?array $roles = []
    ) {
        $this->name = $name;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = empty($roles) ? ['ROLE_USER'] : $roles;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     *
     * @return self
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     *
     * @return self
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     *
     * @return self
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFashionIndustry(): ?bool
    {
        return $this->fashionIndustry;
    }

    /**
     * @param bool|null $fashionIndustry
     *
     * @return User
     */
    public function setFashionIndustry(?bool $fashionIndustry): self
    {
        $this->fashionIndustry = $fashionIndustry;

        return $this;
    }

    /**
     * @return string
     */
    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    /**
     * @param string $jobTitle
     *
     * @return self
     */
    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    /**
     * @param bool|null $newsletter
     *
     * @return self
     */
    public function setNewsletter(?bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSurvey(): ?bool
    {
        return $this->survey;
    }

    /**
     * @param bool $survey
     *
     * @return self
     */
    public function setSurvey(?bool $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVip(): ?bool
    {
        return $this->vip;
    }

    /**
     * @param bool $vip
     *
     * @return self
     */
    public function setVip(?bool $vip): self
    {
        $this->vip = $vip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSector(): ?string
    {
        return $this->sector;
    }

    /**
     * @param string|null $sector
     *
     * @return self
     */
    public function setSector(?string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     *
     * @return self
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     *
     * @return self
     */
    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     *
     * @return self
     */
    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return self
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
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
     *
     * @return self
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     *
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @return string
     */
    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     *
     * @return self
     */
    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }
        if ($this->email !== $user->getEmail()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     *
     * @return self
     */
    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     *
     * @return self
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     *
     * @return self
     */
    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    /**
     * @param string|null $apiToken
     *
     * @return self
     */
    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDuration(): ?string
    {
        return $this->duration;
    }

    /**
     * @param string|null $duration
     *
     * @return self
     */
    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime|null $expiresAt
     *
     * @return self
     */
    public function setExpiresAt(?\DateTime $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}

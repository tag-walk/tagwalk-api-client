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

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Describe showroomUser document.
 *
 * Used for persistance and authentication
 *
 * @see Document
 * @see UserInterface
 */
class ShowroomUser extends User
{
    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank(groups={"showroom_user"})
     */
    private $company;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank(groups={"showroom_user"})
     */
    private $address;

    /**
     * @var string
     * @Assert\Type("string")
     */
    private $note;

    /**
     * @return string
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     */
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }
}

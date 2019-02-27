<?php
/**
 * PHP version 7
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
    private $medias;

    private $streetstyles;

    /**
     * @var User
     * @Assert\Type("object")
     */
    private $user;

    /**
     * @var string
     * @Assert\Type("string")
     */
    private $token;
}

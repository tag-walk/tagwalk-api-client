<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Security;

use DateTime;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class UserChecker implements UserCheckerInterface
{
    final public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        $expiresAt = $user->getExpiresAt();
        if ($expiresAt > new DateTime()) {
            throw new AccountExpiredException('Your user account is expired.');
        }
        if ($user->getStatus() === Status::DISABLED) {
            throw new DisabledException('Your user account is disabled.');
        }
    }

    final public function checkPostAuth(UserInterface $user): void
    {
    }
}

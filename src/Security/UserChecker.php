<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * UserChecker checks the user account flags.
 *
 * @author Julian Finkler (Devtronic) <julian@developer-heaven.de>
 *
 * @final
 */
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(BaseUserInterface $user): void
    {
        if ($user instanceof UserInterface && !$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);
            throw $ex;
        }
    }

    public function checkPostAuth(BaseUserInterface $user, ?TokenInterface $token = null): void
    {
    }
}

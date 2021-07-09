<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Util;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

/**
 * Class updating the hashed password in the user when there is a new password.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class PasswordUpdater implements PasswordUpdaterInterface
{
    private $hasherFactory;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasherFactory = $hasherFactory;
    }

    public function hashPassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();

        if ($plainPassword === '') {
            return;
        }

        $salt = null;
        if ($user instanceof LegacyPasswordAuthenticatedUserInterface) {
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
        }

        $user->setSalt($salt);

        $hasher = $this->hasherFactory->getPasswordHasher(get_class($user));

        if ($hasher instanceof UserPasswordHasherInterface) {
            $hash = $hasher->hashPassword($user, $plainPassword);
        } else {
            if ($hasher instanceof LegacyPasswordHasherInterface) {
                $hash = $hasher->hash($plainPassword, $user->getSalt());
            } else {
                $hash = $hasher->hash($plainPassword);
            }
        }

        $user->setPassword($hash);
        $user->eraseCredentials();
    }
}

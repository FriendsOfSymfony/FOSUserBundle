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
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

/**
 * Class updating the hashed password in the user when there is a new password.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class HashingPasswordUpdater implements PasswordUpdaterInterface
{
    private $passwordHasherFactory;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
    }

    public function hashPassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword || '' === $plainPassword) {
            return;
        }

        $hasher = $this->passwordHasherFactory->getPasswordHasher($user);

        if (!$hasher instanceof LegacyPasswordHasherInterface) {
            $user->setSalt(null);
            $hashedPassword = $hasher->hash($plainPassword);
        } else {
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            $user->setSalt($salt);

            $hashedPassword = $hasher->hash($plainPassword, $salt);
        }

        $user->setPassword($hashedPassword);
        $user->setPlainPassword(null);
    }
}

<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Services\EmailConfirmation\Interfaces;

use FOS\UserBundle\Model\User;

/**
 * Interface EmailUpdateConfirmationInterface.
 */
interface EmailUpdateConfirmationInterface
{
    /**
     * @param string $hashedEmail
     *
     * @return string
     */
    public function fetchEncryptedEmailFromConfirmationLink($hashedEmail);

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user);

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * @param string $confirmationRoute
     *
     * @return $this
     */
    public function setConfirmationRoute($confirmationRoute);
}

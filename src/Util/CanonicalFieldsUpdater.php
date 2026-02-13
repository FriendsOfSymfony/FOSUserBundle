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

/**
 * Class updating the canonical fields of the user.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @final
 */
class CanonicalFieldsUpdater
{
    private $usernameCanonicalizer;
    private $emailCanonicalizer;

    public function __construct(CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer)
    {
        $this->usernameCanonicalizer = $usernameCanonicalizer;
        $this->emailCanonicalizer = $emailCanonicalizer;
    }

    public function updateCanonicalFields(UserInterface $user): void
    {
        $user->setUsernameCanonical($this->canonicalizeUsername($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    /**
     * Canonicalizes an email.
     *
     * @param string|null $email
     *
     * @phpstan-return ($email is null ? null : string)
     */
    public function canonicalizeEmail($email): ?string
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }

    /**
     * Canonicalizes a username.
     *
     * @param string|null $username
     *
     * @phpstan-return ($username is null ? null : string)
     */
    public function canonicalizeUsername($username): ?string
    {
        return $this->usernameCanonicalizer->canonicalize($username);
    }
}

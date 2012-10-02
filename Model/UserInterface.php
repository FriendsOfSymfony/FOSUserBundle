<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface UserInterface extends AdvancedUserInterface, \Serializable
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Sets the username.
     *
     * @param  string $username
     * @return User
     */
    public function setUsername($username);

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical();

    /**
     * Sets the canonical username.
     *
     * @param  string $usernameCanonical
     * @return User
     */
    public function setUsernameCanonical($usernameCanonical);

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the email.
     *
     * @param  string $email
     * @return User
     */
    public function setEmail($email);

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical();

    /**
     * Set the canonical email.
     *
     * @param  string $emailCanonical
     * @return User
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Sets the plain password.
     *
     * @param  string                           $password
     * @return User|\FOS\UserBundle\Propel\User
     */
    public function setPlainPassword($password);

    /**
     * Sets the hashed password.
     *
     * @param  string $password
     * @return User
     */
    public function setPassword($password);

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return Boolean
     */
    public function isSuperAdmin();

    /**
     * Tells if the the given user is this user.
     *
     * Useful when not hydrating all fields.
     *
     * @param null|UserInterface $user
     *
     * @return Boolean
     */
    public function isUser(UserInterface $user = null);

    /**
     * @param  Boolean $boolean
     * @return User
     */
    public function setEnabled($boolean);

    /**
     * Sets the locking status of the user.
     *
     * @param  Boolean $boolean
     * @return User
     */
    public function setLocked($boolean);

    /**
     * Sets the super admin status
     *
     * @param  Boolean                          $boolean
     * @return User|\FOS\UserBundle\Propel\User
     */
    public function setSuperAdmin($boolean);

    /**
     * Gets the confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken();

    /**
     * Sets the confirmation token
     *
     * @param  string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param  null|\DateTime $date
     * @return User
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param integer $ttl Requests older than this many seconds will be considered expired
     *
     * @return Boolean true if the user's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired($ttl);

    /**
     * Sets the last login time
     *
     * @param  \DateTime $time
     * @return User
     */
    public function setLastLogin(\DateTime $time);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return Boolean
     */
    public function hasRole($role);

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param  array $roles
     * @return User
     */
    public function setRoles(array $roles);

    /**
     * Adds a role to the user.
     *
     * @param  string                           $role
     * @return User|\FOS\UserBundle\Propel\User
     */
    public function addRole($role);

    /**
     * Removes a role to the user.
     *
     * @param  string $role
     * @return void
     */
    public function removeRole($role);
}

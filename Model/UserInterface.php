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
    /**
     * Sets the username.
     *
     * @param string $username
     */
    function setUsername($username);

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    function getUsernameCanonical();

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     */
    function setUsernameCanonical($usernameCanonical);

    /**
     * Gets email.
     *
     * @return string
     */
    function getEmail();

    /**
     * Sets the email.
     *
     * @param string $email
     */
    function setEmail($email);

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    function getEmailCanonical();

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     */
    function setEmailCanonical($emailCanonical);

    /**
     * Gets the plain password.
     *
     * @return string
     */
    function getPlainPassword();

    /**
     * Sets the plain password.
     *
     * @param string $password
     */
    function setPlainPassword($password);

    /**
     * Sets the hashed password.
     *
     * @param string $password
     */
    function setPassword($password);

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return Boolean
     */
    function isSuperAdmin();

    /**
     * Tells if the the given user is this user.
     *
     * Useful when not hydrating all fields.
     *
     * @param UserInterface $user
     * @return Boolean
     */
    function isUser(UserInterface $user = null);

    /**
     * @param Boolean $boolean
     */
    function setEnabled($boolean);

    /**
     * Sets the locking status of the user.
     *
     * @param Boolean $boolean
     */
    function setLocked($boolean);

    /**
     * Sets the super admin status
     *
     * @param Boolean $boolean
     */
    function setSuperAdmin($boolean);

    /**
     * Generates the confirmation token if it is not set.
     */
    function generateConfirmationToken();

    /**
     * Gets the confirmation token.
     *
     * @return string
     */
    function getConfirmationToken();

    /**
     * Sets the confirmation token
     *
     * @param string $confirmationToken
     */
    function setConfirmationToken($confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param \DateTime $date
     */
    function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param integer $ttl Requests older than this many seconds will be considered expired
     * @return Boolean true if the user's password request is non expired, false otherwise
     */
    function isPasswordRequestNonExpired($ttl);

    /**
     * Sets the last login time
     *
     * @param \DateTime $time
     */
    function setLastLogin(\DateTime $time);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     * @return Boolean
     */
    function hasRole($role);

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     */
    function setRoles(array $roles);

    /**
     * Adds a role to the user.
     *
     * @param string $role
     */
    function addRole($role);

    /**
     * Removes a role to the user.
     *
     * @param string $role
     */
    function removeRole($role);
}

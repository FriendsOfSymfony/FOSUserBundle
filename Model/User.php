<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Model;

use Symfony\Component\Security\Role\RoleInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\User\AccountInterface;
use Symfony\Component\Security\User\AdvancedAccountInterface;
use Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder;

/**
 * Storage agnostic user object
 * Has validator annotation, but database mapping must be done in a subclass.
 *
 */
abstract class User implements UserInterface
{
    const ROLE_DEFAULT    = 'ROLE_USER';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * The algorithm to use for hashing
     *
     * @var string
     */
    protected $algorithm;

    /**
     * The salt to use for hashing
     *
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * @var boolean
     */
    protected $expired;

    /**
     * @var DateTime
     */
    protected $expiresAt;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var boolean
     */
    protected $credentialsExpired;

    /**
     * @var DateTime
     */
    protected $credentialsExpireAt;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->generateConfirmationToken();
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->roles = array();
        $this->credentialsExpired = false;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === self::ROLE_DEFAULT) {
            return;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * Implementation of AccountInterface.
     *
     * @param AccountInterface $account
     * @return boolean
     */
    public function equals(AccountInterface $account)
    {
        if (!$account instanceof User) {
            return false;
        }

        if ($this->password !== $account->getPassword()) {
            return false;
        }
        if ($this->getSalt() !== $account->getSalt()) {
            return false;
        }
        if ($this->usernameCanonical !== $account->getUsernameCanonical()) {
            return false;
        }
        if ($this->isAccountNonExpired() !== $account->isAccountNonExpired()) {
            return false;
        }
        if (!$this->locked !== $account->isAccountNonLocked()) {
            return false;
        }
        if ($this->isCredentialsNonExpired() !== $account->isCredentialsNonExpired()) {
            return false;
        }
        if ($this->enabled !== $account->isEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Removes sensitive data from the user.
     * Implements AccountInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Return the user unique id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the canonical username in search and sort queries
     *
     * @return string
     **/
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * Implementation of AccountInterface
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the canonical email in search and sort queries
     *
     * @return string
     **/
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Implements AccountInterface
     * Get the encrypted password
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Get confirmationToken
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Return the user roles
     * Implements AccountInterface
     *
     * @return array The roles
     **/
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = self::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->vote('ROLE_USER');
     *
     * @param string $role
     * @return void
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Checks whether the user's account has expired.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired()
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the user is locked.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user is not locked, false otherwise
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     */
    public function isCredentialsNonExpired()
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    public function isCredentialsExpired()
    {
        return !$this->isCredentialsNonExpired();
    }

    /**
     * Checks whether the user is enabled.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    public function isExpired()
    {
        return !$this->isAccountNonExpired();
    }

    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Tell if the the given user has the super admin role
     *
     * @return Boolean
     */
    public function isSuperAdmin()
    {
       return $this->hasRole(self::ROLE_SUPERADMIN);
    }

    /**
     * Tell if the the given user is this user
     * Useful when not hydrating all fields.
     *
     * @param User $user
     * @return boolean
     */
    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->getUsername() === $user->getUsername();
    }

    public function incrementCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * Set username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set usernameCanonical.
     *
     * @param string $usernameCanonical
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    public function setCredentialsExpireAt(\DateTime $date)
    {
        $this->credentialsExpireAt = $date;
    }

    public function setCredentialsExpired($boolean)
    {
        $this->credentialsExpired = $boolean;
    }

    /**
     * Set email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Set emailCanonical.
     *
     * @param string $emailCanonical
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
    }

    /**
     * @param bool $boolean
     */
    public function setEnabled($boolean)
    {
        $this->enabled = $boolean;
    }

    /**
     * Sets this user to expired
     *
     * @param boolean $boolean
     * @return void
     */
    public function setExpired($boolean)
    {
        $this->expired = $boolean;
    }

    public function setExpiresAt(\DateTime $date)
    {
        $this->expiresAt = $date;
    }

    /**
     * Sets the hashed password.
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Sets the super admin status
     *
     * @param boolean $boolean
     * @return void
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(self::ROLE_SUPERADMIN);
        } else {
            $this->removeRole(self::ROLE_SUPERADMIN);
        }
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @param \DateTime $time
     */
    public function setLastLogin(\DateTime $time)
    {
        $this->lastLogin = $time;
    }

    public function setLocked($boolean)
    {
        $this->locked = $boolean;
    }

    /**
     * Set confirmationToken
     *
     * @param  string
     * @return null
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * Set the timestamp that the user requested a password reset.
     *
     * @param DateTime $date
     */
    public function setPasswordRequestedAt(\DateTime $date)
    {
        $this->passwordRequestedAt = $date;
    }

    /**
     * Get the timestamp that the user requested a password reset.
     *
     * @return DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Checks whether the password reset request has expired.
     *
     * @param integer $ttl Requests older than this many seconds will be considered expired
     * @return boolean true if the users's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->passwordRequestedAt instanceof \DateTime &&
               $this->passwordRequestedAt->getTimestamp() + $ttl > time();
    }

    /**
     * Generate confirmationToken if it is not set
     *
     * @return null
     */
    public function generateConfirmationToken()
    {
        if (null === $this->confirmationToken) {
            $bytes = false;
            if (function_exists('openssl_random_pseudo_bytes') && 0 !== stripos(PHP_OS, 'win')) {
                $bytes = openssl_random_pseudo_bytes(32, $strong);

                if (true !== $strong) {
                    $bytes = false;
                }
            }

            // let's just hope we got a good seed
            if (false === $bytes) {
                $bytes = hash('sha256', uniqid(mt_rand(), true), true);
            }

            $this->confirmationToken = base_convert(bin2hex($bytes), 16, 36);
        }
    }

    public function setRoles(array $roles)
    {
          $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * Get groups granted to the user
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * Gets the name of the groups which includes the user
     *
     * @return array
     */
    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * Indicates whether the user belongs to the specified group or not
     *
     * @param string $name Name of the group
     * @return boolean
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * Add a group to the user groups
     *
     * @param GroupInterface $group
     * @return null
     **/
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }
    }

    /**
     * Remove a group from the user groups
     *
     * @param GroupInterface $group
     * @return null
     **/
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->remove($group);
        }
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }
}

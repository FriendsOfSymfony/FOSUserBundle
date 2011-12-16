<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Propel;

use FOS\UserBundle\Model\User as FosUser;
use FOS\UserBundle\Model\GroupInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

class UserProxy extends FosUser
{
    /**
     * @var \FOS\UserBundle\Propel\User
     */
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
        if ($user->isNew()) {
            $this->user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $this->generateConfirmationToken();
            $this->setEnabled(false);
            $this->setCredentialsExpired(false);
            $this->setLocked(false);
            $this->setExpired(false);
            $this->user->setRoles(array());
        }
    }

    public function getPropelUser()
    {
        return $this->user;
    }

    public function __call($method, $arguments)
    {
        if (is_callable(array($this->getPropelUser(), $method))) {
            return call_user_func_array(array($this->getPropelUser(), $method), $arguments);
        }

        throw new \BadMethodCallException('Can\'t call method '.$method);
    }

    public function updateParent()
    {
        parent::setEmail($this->getEmail());
        parent::setEmailCanonical($this->getEmailCanonical());
        parent::setPassword($this->getPassword());
        parent::setUsername($this->getUsername());
        parent::setUsernameCanonical($this->getUsernameCanonical());
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->user);
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->user = unserialize($serialized);
    }

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->user->getId();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->user->getUsername();
    }

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical()
    {
        return $this->user->getUsernameCanonical();
    }

    /**
     * Implementation of SecurityUserInterface
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->user->getSalt();
    }

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->user->getEmail();
    }

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->user->getEmailCanonical();
    }

    /**
     * Gets the encrypted password.
     *
     * Implements SecurityUserInterface
     * @return string
     */
    public function getPassword()
    {
        return $this->user->getPassword();
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->user->getLastLogin();
    }

    /**
     * Gets the confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->user->getConfirmationToken();
    }

    /**
     * Returns the user roles
     *
     * Implements SecurityUserInterface
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->user->getRoles();

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired()
    {
        if (true === $this->user->getExpired()) {
            return false;
        }

        if (null !== $this->user->getExpiresAt() && $this->user->getExpiresAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user is not locked, false otherwise
     */
    public function isAccountNonLocked()
    {
        return !$this->user->getLocked();
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     */
    public function isCredentialsNonExpired()
    {
        if (true === $this->user->getCredentialsExpired()) {
            return false;
        }

        if (null !== $this->user->getCredentialsExpireAt() && $this->user->getCredentialsExpireAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->user->getEnabled();
    }

    /**
     * Sets the username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        parent::setUsername($username);
        $this->user->setUsername($username);
    }

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        parent::setUsernameCanonical($usernameCanonical);
        $this->user->setUsernameCanonical($usernameCanonical);
    }

    public function setCredentialsExpireAt(\DateTime $date)
    {
        $this->user->setCredentialsExpireAt($date);
    }

    public function setCredentialsExpired($boolean)
    {
        $this->user->setCredentialsExpired($boolean);
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->user->setEmail($email);
    }

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     */
    public function setEmailCanonical($emailCanonical)
    {
        parent::setEmailCanonical($emailCanonical);
        $this->user->setEmailCanonical($emailCanonical);
    }

    /**
     * @param Boolean $boolean
     */
    public function setEnabled($boolean)
    {
        parent::setEnabled($boolean);
        $this->user->setEnabled((Boolean) $boolean);
    }

    /**
     * Sets this user to expired.
     *
     * @param Boolean $boolean
     */
    public function setExpired($boolean)
    {
        parent::setExpired($boolean);
        $this->user->setExpired($boolean);
    }

    public function setExpiresAt(\DateTime $date)
    {
        parent::setExpiredAt($date);
        $this->user->setExpiresAt($date);
    }

    /**
     * Sets the hashed password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        parent::setPassword($password);
        $this->user->setPassword($password);
    }

    /**
     * Sets the plain password.
     *
     * @param string $password
     */
    public function setPlainPassword($password)
    {
        parent::setPlainPassword($password);
        $this->plainPassword = $password;
    }

    /**
     * Sets the last login time
     *
     * @param \DateTime $time
     */
    public function setLastLogin(\DateTime $time)
    {
        parent::setLastLogin($time);
        $this->user->setLastLogin($time);
    }

    /**
     * Sets the locking status of the user.
     *
     * @param Boolean $boolean
     */
    public function setLocked($boolean)
    {
        parent::setLocked($boolean);
        $this->user->setLocked($boolean);
    }

    /**
     * Sets the confirmation token
     *
     * @param string $confirmationToken
     */
    public function setConfirmationToken($confirmationToken)
    {
        parent::setConfirmationToken($confirmationToken);
        $this->user->setConfirmationToken($confirmationToken);
    }

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param \DateTime $date
     */
    public function setPasswordRequestedAt(\DateTime $date)
    {
        parent::setPasswordRequestedAt($date);
        $this->user->setPasswordRequestedAt($date);
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->user->getPasswordRequestedAt();
    }

    /**
     * Adds a role to the user.
     *
     * @param string $role
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return;
        }

        $this->user->addRole($role);
    }

    /**
     * Removes a role to the user.
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        $this->user->removeRole($role);
    }

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->user->setRoles(array());

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * Gets the groups granted to the user.
     *
     * @return Collection
     */
    public function getGroups()
    {
        return new GroupCollection($this->user->getGroups());
    }
}

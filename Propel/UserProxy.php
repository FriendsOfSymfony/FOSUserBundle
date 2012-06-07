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
     * @return UserProxy
     */
    public function setUsername($username)
    {
        $this->user->setUsername($username);

        return $this;
    }

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     * @return UserProxy
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->user->setUsernameCanonical($usernameCanonical);

        return $this;
    }

    public function setCredentialsExpireAt(\DateTime $date)
    {
        $this->user->setCredentialsExpireAt($date);

        return $this;
    }

    public function setCredentialsExpired($boolean)
    {
        $this->user->setCredentialsExpired($boolean);

        return $this;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     * @return UserProxy
     */
    public function setEmail($email)
    {
        $this->user->setEmail($email);

        return $this;
    }

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     * @return UserProxy
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->user->setEmailCanonical($emailCanonical);

        return $this;
    }

    /**
     * @param Boolean $boolean
     * @return UserProxy
     */
    public function setEnabled($boolean)
    {
        $this->user->setEnabled((Boolean) $boolean);

        return $this;
    }

    /**
     * Sets this user to expired.
     *
     * @param Boolean $boolean
     * @return UserProxy
     */
    public function setExpired($boolean)
    {
        $this->user->setExpired($boolean);

        return $this;
    }

    /**
     * @param \DateTime $date
     * @return UserProxy
     */
    public function setExpiresAt(\DateTime $date)
    {
        $this->user->setExpiresAt($date);

        return $this;
    }

    /**
     * Sets the hashed password.
     *
     * @param string $password
     * @return UserProxy
     */
    public function setPassword($password)
    {
        $this->user->setPassword($password);

        return $this;
    }

    /**
     * Sets the plain password.
     *
     * @param string $password
     * @return UserProxy
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Sets the last login time
     *
     * @param \DateTime $time
     * @return UserProxy
     */
    public function setLastLogin(\DateTime $time)
    {
        $this->user->setLastLogin($time);

        return $this;
    }

    /**
     * Sets the locking status of the user.
     *
     * @param Boolean $boolean
     * @return UserProxy
     */
    public function setLocked($boolean)
    {
        $this->user->setLocked($boolean);

        return $this;
    }

    /**
     * Sets the confirmation token
     *
     * @param string $confirmationToken
     * @return UserProxy
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->user->setConfirmationToken($confirmationToken);

        return $this;
    }

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param \DateTime $date
     * @return UserProxy
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->user->setPasswordRequestedAt($date);

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return \DateTime
     * @return UserProxy
     */
    public function getPasswordRequestedAt()
    {
        return $this->user->getPasswordRequestedAt();
    }

    /**
     * Adds a role to the user.
     *
     * @param string $role
     * @return UserProxy
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        $this->user->addRole($role);

        return $this;
    }

    /**
     * Removes a role to the user.
     *
     * @param string $role
     * @return UserProxy
     */
    public function removeRole($role)
    {
        $this->user->removeRole($role);

        return $this;
    }

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     * @return UserProxy
     */
    public function setRoles(array $roles)
    {
        $this->user->setRoles(array());

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
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

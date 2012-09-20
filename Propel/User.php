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

use FOS\UserBundle\Model\GroupableInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Propel\om\BaseUser;

class User extends BaseUser implements UserInterface, GroupableInterface
{
    /**
     * Plain password. Used when changing the password. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    public function __construct()
    {
        parent::__construct();

        if ($this->isNew()) {
            $this->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->salt,
                $this->password,
                $this->expired,
                $this->locked,
                $this->credentials_expired,
                $this->enabled,
                $this->_new,
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 1, null));

        list(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
            $this->expired,
            $this->locked,
            $this->credentials_expired,
            $this->enabled,
            $this->_new
        ) = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritDoc}
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
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
        $roles = parent::getRoles();

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        parent::addRole($role);

        return $this;
    }

    public function hasRole($value)
    {
        return parent::hasRole(strtoupper($value));
    }

    public function removeRole($value)
    {
        return parent::removeRole(strtoupper($value));
    }

    public function setRoles(array $v)
    {
        foreach ($v as $i => $role) {
            $v[$i] = strtoupper($role);
        }

        return parent::setRoles($v);
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        if (true === $this->getExpired()) {
            return false;
        }

        if (null !== $this->getExpiresAt() && $this->getExpiresAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->getLocked();
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        if (true === $this->getCredentialsExpired()) {
            return false;
        }

        if (null !== $this->getCredentialsExpireAt() && $this->getCredentialsExpireAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritDoc}
     */
    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->getId() === $user->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setSuperAdmin($boolean)
    {
        if ($boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * Gets the name of the groups which includes the user.
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
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return Boolean
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }
}

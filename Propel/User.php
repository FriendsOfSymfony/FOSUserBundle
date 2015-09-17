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

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->isNew()) {
            $this->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->salt,
                $this->password,
                $this->locked,
                $this->enabled,
                $this->_new,
            )
        );
    }

    /**
     * {@inheritdoc}
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
            $this->locked,
            $this->enabled,
            $this->_new
        ) = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function hasRole($value)
    {
        return parent::hasRole(strtoupper($value));
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($value)
    {
        return parent::removeRole(strtoupper($value));
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $v)
    {
        foreach ($v as $i => $role) {
            $v[$i] = strtoupper($role);
        }

        return parent::setRoles($v);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        if (null !== $this->getExpiresAt() && $this->getExpiresAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->getLocked();
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        if (null !== $this->getCredentialsExpireAt() && $this->getCredentialsExpireAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }
}

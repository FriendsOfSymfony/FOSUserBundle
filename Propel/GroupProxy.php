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

use FOS\UserBundle\Model\GroupInterface;

class GroupProxy implements GroupInterface
{
    protected $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function getPropelGroup()
    {
        return $this->group;
    }

    public function __call($method, $arguments)
    {
        if (is_callable(array($this->getPropelGroup(), $method))) {
            return call_user_func_array(array($this->getGroup(), $method), $arguments);
        }

        throw new \BadMethodCallException('Can\'t call method '.$method);
    }

    public function getId()
    {
        return $this->group->getId();
    }

    public function getName()
    {
        return $this->group->getName();
    }

    public function setName($name)
    {
        $this->group->setName($name);
    }

    public function hasRole($role)
    {
        return $this->group->hasRole(strtoupper($role));
    }

    public function getRoles()
    {
        return $this->group->getRoles();
    }

    public function removeRole($role)
    {
        $this->group->removeRole(strtoupper($role));
    }

    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->group->addRole(strtoupper($role));
        }
    }

    public function setRoles(array $roles)
    {
        $this->group->setRoles(array_map('strtoupper', $roles));
    }
}

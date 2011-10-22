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

use FOS\UserBundle\Model\Group as ModelGroup;

class GroupProxy extends ModelGroup
{
    protected $group;
    protected $roles;

    public function __construct(Group $group)
    {
        $this->group = $group;
        $this->updateFormPropel();
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function __call($method, $arguments)
    {
        if (is_callable(array($this->getGroup(), $method))) {
            return call_user_func_array(array($this->getGroup(), $method), $arguments);
        }

        throw new \BadMethodCallException('Can\'t call method '.$method);
    }

    public function save()
    {
        $this->updatePropelGroup();
        $this->getGroup()->save();

        return $this;
    }

    protected function updateFormPropel()
    {
        $group = $this->getGroup();

        $this->name = $group->getName();

        $this->roles = array();
        foreach ($group->getRoles() as $role) {
            $this->roles[] = $role->getName();
        }
    }

    protected function updatePropelGroup()
    {
        $group = $this->getGroup();

        $group->setName($this->name);

        $collection = new \PropelObjectCollection();
        $collection->setModel(RolePeer::OM_CLASS);
        foreach ($this->roles as $role) {
            $roleObject = RoleQuery::create()->findOneByName($role);
            if (!$roleObject) {
                $roleObject = new Role();
                $roleObject->setName($role);
                $roleObject->save();
            }

            $collection[] = $roleObject;
        }
        $group->setRoles($collection);
    }
}

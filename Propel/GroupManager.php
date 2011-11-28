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
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\GroupManager as BaseGroupManager;

class GroupManager extends BaseGroupManager
{
    protected $class;

    protected $modelClass;

    public function __construct($proxyClass, $modelClass)
    {
        $this->class = $proxyClass;
        $this->modelClass = $modelClass;
    }

    /**
    * Returns an empty group instance.
    *
    * @param string $name
    * @return GroupInterface
    */
    public function createGroup($name)
    {
        $class = $this->modelClass;
        $group = new $class();
        $group->setName($name);

        return $this->proxyfy($group);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteGroup(GroupInterface $group)
    {
        if (!$group instanceof GroupProxy) {
            throw new \InvalidArgumentException('This group instance is not supported by the Propel GroupManager implementation');
        }

        $group->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * {@inheritDoc}
     */
    public function findGroupBy(array $criteria)
    {
        $query = $this->createQuery();

        foreach ($criteria as $field => $value) {
        	$method = 'filterBy'.ucfirst($field);
            $query->$method($value);
        }

        $group = $query->findOne();

        if ($group) {
            $group = $this->proxyfy($group);
        }

        return $group;
    }

    /**
     * {@inheritDoc}
     */
    public function findGroups()
    {
        return $this->createQuery()->find();
    }

    /**
     * Updates a group
     *
     * @param GroupInterface $group
     * @return void
     */
    public function updateGroup(GroupInterface $group)
    {
        if (!$group instanceof GroupProxy) {
            throw new \InvalidArgumentException('This group instance is not supported by the Propel GroupManager implementation');
        }

        $group->save();
    }

    /**
    * Create the propel query class corresponding to your queryclass
    *
    * @return \ModelCriteria the queryClass
    */
    protected function createQuery()
    {
        return \PropelQuery::from($this->modelClass);
    }

    protected function proxyfy(Group $group)
    {
        $proxyClass = $this->getClass();
        $proxy = new $proxyClass($group);

        return $proxy;
    }
}

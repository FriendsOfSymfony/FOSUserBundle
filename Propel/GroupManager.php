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
use FOS\UserBundle\Model\GroupManager as BaseGroupManager;

class GroupManager extends BaseGroupManager
{
    /**
     * @var string
     */
    protected $class;

    /**
     * GroupManager constructor.
     *
     * @param $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function createGroup($name)
    {
        $class = $this->class;
        $group = new $class();
        $group->setName($name);

        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup(GroupInterface $group)
    {
        if (!$group instanceof \Persistent) {
            throw new \InvalidArgumentException('This group instance is not supported by the Propel GroupManager implementation');
        }

        $group->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupBy(array $criteria)
    {
        $query = $this->createQuery();

        foreach ($criteria as $field => $value) {
            $method = 'filterBy'.ucfirst($field);
            $query->$method($value);
        }

        return $query->findOne();
    }

    /**
     * {@inheritdoc}
     */
    public function findGroups()
    {
        return $this->createQuery()->find();
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(GroupInterface $group)
    {
        if (!$group instanceof \Persistent) {
            throw new \InvalidArgumentException('This group instance is not supported by the Propel GroupManager implementation');
        }

        $group->save();
    }

    /**
     * Create the propel query class corresponding to your queryclass.
     *
     * @return \ModelCriteria the queryClass
     */
    protected function createQuery()
    {
        return \PropelQuery::from($this->class);
    }
}

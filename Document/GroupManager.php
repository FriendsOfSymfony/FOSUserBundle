<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Document;

use FOS\UserBundle\Model\GroupInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\UserBundle\Model\GroupManager as BaseGroupManager;

class GroupManager extends BaseGroupManager
{
    protected $dm;
    protected $class;
    protected $repository;

    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteGroup(GroupInterface $group)
    {
        $this->dm->remove($group);
        $this->dm->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findGroupBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findGroups()
    {
        return $this->repository->findAll();
    }

    /**
     * Updates a group.
     *
     * @param GroupInterface $group
     * @param Boolean        $andFlush Whether to flush the changes (default true)
     */
    public function updateGroup(GroupInterface $group, $andFlush = true)
    {
        $this->dm->persist($group);
        if ($andFlush) {
            $this->dm->flush();
        }
    }
}

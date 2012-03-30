<?php

namespace FOS\UserBundle\CouchDocument;

use FOS\UserBundle\Model\GroupInterface;
use Doctrine\ODM\CouchDB\DocumentManager;
use FOS\UserBundle\Model\GroupManager as BaseGroupManager;

class GroupManager extends BaseGroupManager
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var DocumentRepository
     */
    protected $repository;

    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Deletes a group.
     *
     * @param GroupInterface $group
     * @return void
     */
    public function deleteGroup(GroupInterface $group)
    {
        $this->dm->remove($group);
        $this->dm->flush();
    }

    /**
     * Finds one group by the given criteria.
     *
     * @param array $criteria
     * @return GroupInterface
     */
    public function findGroupBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Returns a collection with all user instances.
     *
     * @return \Traversable
     */
    public function findGroups()
    {
        return $this->repository->findAll();
    }

    /**
     * Returns the group's fully qualified class name.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Updates a group.
     *
     * @param GroupInterface $group
     */
    public function updateGroup(GroupInterface $group, $andFlush = true)
    {
        $this->dm->persist($group);
        if ($andFlush) {
            $this->dm->flush();
        }
    }
}

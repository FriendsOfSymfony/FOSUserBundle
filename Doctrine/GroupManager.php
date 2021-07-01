<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Doctrine;

@trigger_error('Using Groups is deprecated since version 2.2 and will be removed in 3.0.', E_USER_DEPRECATED);

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManager as BaseGroupManager;

/**
 * @deprecated
 */
class GroupManager extends BaseGroupManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * GroupManager constructor.
     *
     * @param string $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup(GroupInterface $group)
    {
        $this->objectManager->remove($group);
        $this->objectManager->flush();
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
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findGroups()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(GroupInterface $group, $andFlush = true)
    {
        $this->objectManager->persist($group);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}

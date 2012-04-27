<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Model;

use FOS\UserBundle\Events;
use FOS\UserBundle\Event\GroupEvent;
use FOS\UserBundle\Event\GroupPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract Group Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
abstract class GroupManager implements GroupManagerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns an empty group instance.
     *
     * @param string $name
     * @return GroupInterface
     */
    public function createGroup($name)
    {
        $class = $this->getClass();
        $group = new $class($name);

        if (null !== $this->dispatcher) {
            $event = new GroupPersistEvent($group);
            $this->dispatcher->dispatch(Events::GROUP_CREATE, $event);

            return $event->getGroup();
        }

        return $group;
    }

    /**
     * Actual updating of the group. To be implemented by the real
     * manager classes.
     *
     * @param GroupInterface $group
     */
    abstract protected function doUpdateGroup(GroupInterface $group);

    /**
     * Updates a group
     *
     * @param GroupInterface $group
     * @param Boolean        $andFlush Whether to flush the changes (default true)
     */
    public function updateGroup(GroupInterface $group, $andFlush = true)
    {
        if (null !== $this->dispatcher) {
            $event = new GroupPersistEvent($group);
            $this->dispatcher->dispatch(Events::GROUP_PRE_PERSIST, $event);

            if ($event->isPersistenceAborted()) {
                return;
            }
        }

        $this->doUpdateGroup($group, $andFlush);

        if (null !== $this->dispatcher) {
            $event = new GroupEvent($group);
            $this->dispatcher->dispatch(Events::GROUP_POST_PERSIST, $event);
        }
    }

    /**
     * This function is to be implemented and perform the actual deletion
     * of the group object.
     *
     * @param GroupInterface $group
     */
    abstract protected function doDeleteGroup(GroupInterface $group);

    /**
     * Deletes a group.
     *
     * @param GroupInterface $group
     * @param Boolean $andFlush
     * @return void
     */
    public function deleteGroup(GroupInterface $group, $andFlush = true)
    {
        if (null !== $this->dispatcher) {
            $event = new GroupPersistEvent($group);
            $this->dispatcher->dispatch(Events::GROUP_PRE_DELETE, $event);

            if ($event->isPersistenceAborted()) {
                return;
            }
        }

        $this->doDeleteGroup($group, $andFlush);

        if (null !== $this->dispatcher) {
            $event = new GroupEvent($group);
            $this->dispatcher->dispatch(Events::GROUP_POST_DELETE, $event);
        }
    }

    /**
     * Finds a group by name.
     *
     * @param string $name
     * @return GroupInterface
     */
    public function findGroupByName($name)
    {
        return $this->findGroupBy(array('name' => $name));
    }
}

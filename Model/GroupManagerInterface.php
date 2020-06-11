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

@trigger_error('Using Groups is deprecated since version 2.2 and will be removed in 3.0.', E_USER_DEPRECATED);

/**
 * Interface to be implemented by group managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to groups should happen through this interface.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @deprecated
 */
interface GroupManagerInterface
{
    /**
     * Returns an empty group instance.
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function createGroup($name);

    /**
     * Deletes a group.
     */
    public function deleteGroup(GroupInterface $group);

    /**
     * Finds one group by the given criteria.
     *
     * @return GroupInterface
     */
    public function findGroupBy(array $criteria);

    /**
     * Finds a group by name.
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function findGroupByName($name);

    /**
     * Returns a collection with all group instances.
     *
     * @return \Traversable
     */
    public function findGroups();

    /**
     * Returns the group's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a group.
     */
    public function updateGroup(GroupInterface $group);
}

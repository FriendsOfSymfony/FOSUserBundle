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
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @deprecated
 */
interface GroupableInterface
{
    /**
     * Gets the groups granted to the user.
     *
     * @return \Traversable
     */
    public function getGroups();

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return array
     */
    public function getGroupNames();

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return bool
     */
    public function hasGroup($name);

    /**
     * Add a group to the user groups.
     *
     * @return static
     */
    public function addGroup(GroupInterface $group);

    /**
     * Remove a group from the user groups.
     *
     * @return static
     */
    public function removeGroup(GroupInterface $group);
}

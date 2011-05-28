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

/**
 * Abstract Group Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
abstract class GroupManager implements GroupManagerInterface
{
    /**
     * Returns an empty group instance.
     *
     * @param string $name
     * @return GroupInterface
     */
    public function createGroup($name)
    {
        $class = $this->getClass();

        return new $class($name);
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

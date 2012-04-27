<?php

/**
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Event;

use FOS\UserBundle\Model\GroupInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a group.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class GroupEvent extends Event
{
    private $group;

    /**
     * Constructs an event.
     *
     * @param \FOS\UserBundle\Model\GroupInterface $group
     */
    public function __construct(GroupInterface $group)
    {
        $this->group = $group;
    }

    /**
     * Returns the group for this event.
     *
     * @return \FOS\UserBundle\Model\GroupInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Sets the group to be used for the event.
     *
     * @param \FOS\UserBundle\Model\GroupInterface $group
     */
    public function setGroup(GroupInterface $group)
    {
        $this->group = $group;
    }
}

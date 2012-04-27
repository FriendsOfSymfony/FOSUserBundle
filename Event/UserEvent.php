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

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a user.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class UserEvent extends Event
{
    private $user;

    /**
     * Constructs an event.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Returns the user for this event.
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the user to be used for the event.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}

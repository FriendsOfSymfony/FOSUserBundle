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
 * An event related to a persisting event that can be
 * cancelled by a listener.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class GroupPersistEvent extends GroupEvent
{
    /**
     * @var bool
     */
    private $abortPersistence = false;

    /**
     * Indicates that the persisting operation should not proceed.
     */
    public function abortPersistence()
    {
        $this->abortPersistence = true;
    }

    /**
     * Checks if a listener has set the event to abort the persisting
     * operation.
     *
     * @return bool
     */
    public function isPersistenceAborted()
    {
        return $this->abortPersistence;
    }
}

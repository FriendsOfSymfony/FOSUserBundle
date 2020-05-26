<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\Event as ContractsEvent;

// BC layer for Symfony < 4.3
if (class_exists(ContractsEvent::class)) {
    class EventBC extends ContractsEvent
    {
    }
} else {
    class EventBC extends Event
    {
    }
}

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

use FOS\UserBundle\Model\GroupInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class GroupEvent extends Event
{
    private $group;
    private $request;

    public function __construct(GroupInterface $group, Request $request)
    {
        $this->group = $group;
        $this->request = $request;
    }

    /**
     * @return GroupInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}

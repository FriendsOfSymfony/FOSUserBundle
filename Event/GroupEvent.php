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

@trigger_error('Using Groups is deprecated since version 2.2 and will be removed in 3.0.', E_USER_DEPRECATED);

use FOS\UserBundle\Model\GroupInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated
 */
class GroupEvent extends Event
{
    /**
     * @var GroupInterface
     */
    private $group;

    /**
     * @var Request
     */
    private $request;

    /**
     * GroupEvent constructor.
     */
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

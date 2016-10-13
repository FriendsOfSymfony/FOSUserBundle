<?php

namespace FOS\UserBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class GetResponseSessionUserEvent extends GetResponseUserEvent
{
    /**
     * @var UserInterface
     */
    private $sessionUser;

    /**
     * GetResponseSessionUserEvent constructor.
     *
     * @param UserInterface $user
     * @param UserInterface $sessionUser
     * @param Request       $request
     */
    public function __construct(UserInterface $user = null, UserInterface $sessionUser = null, Request $request = null)
    {
        $this->user = $user;
        $this->sessionUser = $sessionUser;
        $this->request = $request;
    }


    /**
     * @return UserInterface
     */
    public function getSessionUser()
    {
        return $this->sessionUser;
    }
}

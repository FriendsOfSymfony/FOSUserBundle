<?php
/**
 * This file is part of Rocketgraph service
 * <http://www.rocketgraph.com>
 */

namespace FOS\UserBundle\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of GetResponseNullableUserEvent
 *
 * @author K.Christofilos <kostas.christofilos@rocketgraph.com>
 */
class GetResponseNullableUserEvent extends GetResponseUserEvent
{
    private $request;
    private $user;

    public function __construct(Request $request, UserInterface $user = null)
    {
        $this->user = $user;
        $this->request = $request;
    }
}
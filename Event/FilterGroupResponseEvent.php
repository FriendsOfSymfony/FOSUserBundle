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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterGroupResponseEvent extends GroupEvent
{
    private $response;

    public function __construct(GroupInterface $group, Request $request, Response $response)
    {
        parent::__construct($group, $request);

        $this->response = $response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}

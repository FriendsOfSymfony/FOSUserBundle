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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class ViewEvent extends Event
{
    private $context;
    private $request;
    private $response;

    public function __construct(array $context, Request $request)
    {
        $this->context = $context;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the response object
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    /**
     * Returns whether a response was set
     *
     * @return Boolean Whether a response was set
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}

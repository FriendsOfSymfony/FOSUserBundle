<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class RequestFormHandler
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Request
     */
    private $request;

    /**
     * @return Request
     */
    protected function getRequest()
    {
        if ($this->request) {
            return $this->request;
        } elseif ($this->requestStack) {
            return $this->requestStack->getCurrentRequest();
        }
        return null;
    }

    public function setRequestStack(RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }
}

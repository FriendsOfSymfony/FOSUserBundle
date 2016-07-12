<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * ContainerAware controller.
 */
abstract class ContainerAwareController implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get request
     *
     * @return Request
     */
    protected function getRequest()
    {
        if ($this->container->has("request")) {
            $request = $this->container->get("request");
        } elseif ($this->container->has("request_stack")) {
            $request = $this->container->get("request_stack")->getCurrentRequest();
        } else {
            $request = null;
        }
        return $request;
    }

    /**
     * Get token
     *
     * @return TokenInterface
     */
    protected function getToken()
    {
        if ($this->container->has('security.context')) {
            $token = $this->container->get('security.context')->getToken();
        } else {
            $token = $this->container->get('security.token_storage')->getToken();
        }
        return $token;
    }
}

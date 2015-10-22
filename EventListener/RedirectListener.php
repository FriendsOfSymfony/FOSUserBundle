<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Redirect to a custom URL in response to FormEvent dispatches.
 */
class RedirectListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $route;

    /**
     * @param RouterInterface $router
     * @param string $route
     */
    public function __construct(RouterInterface $router, $route)
    {
        $this->router = $router;
        $this->route = $route;
    }

    /**
     * @param FormEvent $event
     */
    public function redirect(FormEvent $event)
    {
        $event->setResponse(new RedirectResponse($this->router->generate($this->route)));
    }
}

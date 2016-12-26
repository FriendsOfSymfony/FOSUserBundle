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

use FOS\UserBundle\Event\GetResponseSessionUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UserSessionListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var string
     */
    private $redirectRoute;

    /**
     * ResettingListener constructor.
     *
     * @param UrlGeneratorInterface $router
     * @param string                $redirectRoute
     */
    public function __construct(UrlGeneratorInterface $router, $redirectRoute)
    {
        $this->router = $router;
        $this->redirectRoute = $redirectRoute;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_RESET_REQUEST => 'onCheckSessionUser',
            FOSUserEvents::RESETTING_RESET_INITIALIZE => 'onCheckSessionUser',
            FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE => 'onCheckSessionUser',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onCheckSessionUser',
            FOSUserEvents::REGISTRATION_CHECK => 'onCheckSessionUser',
            FOSUserEvents::REGISTRATION_CONFIRM => 'onCheckSessionUser',
            FOSUserEvents::SECURITY_LOGIN_INITIALIZE => 'onCheckSessionUser',
        );
    }

    /**
     * @param GetResponseSessionUserEvent $event
     */
    public function onCheckSessionUser(GetResponseSessionUserEvent $event)
    {
        if ($this->redirectRoute && $event->getSessionUser() instanceof UserInterface) {
            $url = $this->router->generate($this->redirectRoute);
            $event->setResponse(new RedirectResponse($url));
        }
    }
}

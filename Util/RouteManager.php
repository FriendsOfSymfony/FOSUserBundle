<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\RouteEvent;

/**
 *
 * @author leonardo proietti (leonardo.proietti@gmail.com)
 */
class RouteManager implements EventSubscriberInterface
{
    protected $container;
    
    /**
     * @param ContainerInterface $serviceContainer 
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    static public function getSubscribedEvents()
    {
        return array(
            'fos_user' => array(
                array('onFosUserRegistrationSuccess', 0)
            )
        );
    }
    
    public function onFosUserRegistrationSuccess(RouteEvent $event)
    {
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled'); 
        
        if ($confirmationEnabled) {
            $route = 'fos_user_registration_check_email';
        } else { 
            $route = 'fos_user_registration_confirmed';
        }
        
        $event->setRoute($route);
    }
}
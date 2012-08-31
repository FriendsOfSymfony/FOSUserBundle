<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Security\LoginManagerInterface;
use FOS\UserBundle\Event\ManualLoginEvent;

/**
 *
 * @author leonardo proietti (leonardo.proietti@gmail.com)
 */
class ManualLoginManager implements EventSubscriberInterface
{
    protected $loginManager;
    
    /**
     * @param ContainerInterface $serviceContainer 
     */
    public function __construct(LoginManagerInterface $loginManager)
    {
        $this->loginManager = $loginManager;
    }
    
    static public function getSubscribedEvents()
    {
        return array(
            'fos_user' => array(
                array('onFosUserManualLogin', 20)
            )
        );
    }
    
    public function onFosUserManualLogin(ManualLoginEvent $event)
    {
        try {
            $this->loginManager->loginUser($event->getFirewall(), $event->getUser(), $event->getResponse());
            
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
            
            $event->stopPropagation();
        }
    }
}
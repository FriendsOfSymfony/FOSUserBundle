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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;

/**
 *
 * @author leonardo proietti (leonardo.proietti@gmail.com)
 */
class ManualLoginEvent extends Event
{
    protected $user;
    
    protected $response;
    
    protected $firewall;
    
    protected $isProcessed = false;

    public function __construct(UserInterface $user, Response $response, $firewall = null)
    {
        $this->user = $user;
        $this->response = $response;
        $this->firewall = $firewall;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getResponse()
    {
        return $this->response;
    }
    
    public function getFirewall()
    {
        return $this->firewall;
    }
}
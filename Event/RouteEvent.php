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

/**
 *
 * @author leonardo proietti (leonardo.proietti@gmail.com)
 */
class RouteEvent extends Event
{
    protected $route;
    
    protected $isProcessed = false;
    
    public function setRoute($route)
    {
        $this->route = $route;
        $this->isProcessed = true;
    }
    
    public function getRoute()
    {
        return $this->route;
    }
    
    public function isProcessed()
    {
        return $this->isProcessed;
    }
}
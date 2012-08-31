<?php

namespace FOS\UserBundle\Tests\Event;

use FOS\UserBundle\Event\RouteEvent;

class RouteEventTest extends \PHPUnit_Framework_TestCase
{    
    public function setUp()
    {
        $this->event = new RouteEvent();
        $this->event->setRoute('my_route');
    }
    
    public function testGetRoute()
    {        
        $result = $this->event->getRoute();
        $this->assertEquals('my_route', $result);
    }    
}
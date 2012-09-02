<?php

namespace FOS\UserBundle\Tests\Event;

use FOS\UserBundle\Event\ManualLoginEvent;

class ManualLoginEventTest extends \PHPUnit_Framework_TestCase
{    
    public function setUp()
    {
        $this->user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->disableOriginalConstructor()->getMock();
        $this->response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->disableOriginalConstructor()->getMock();
        $this->firewall = "firewall";
        $this->event = new ManualLoginEvent($this->user, $this->response, $this->firewall);
    }
    
    public function testGetUser()
    {
        $result = $this->event->getUser();
        $this->assertEquals($this->user, $result);
    }    
    
    public function testGetResponse()
    {
        $result = $this->event->getResponse();
        $this->assertEquals($this->response, $result);
    }   
    
    public function testGetFirewall()
    {
        $result = $this->event->getFirewall();
        $this->assertEquals($this->firewall, $result);
    } 
}
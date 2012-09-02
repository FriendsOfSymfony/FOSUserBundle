<?php

namespace FOS\UserBundle\Tests\Security;

use FOS\UserBundle\Security\ManualLoginManager;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

class ManualLoginManagerTest extends \PHPUnit_Framework_TestCase
{    
    public function setUp()
    {
        $this->loginManager = $this->getMockBuilder('FOS\UserBundle\Security\LoginManagerInterface')->disableOriginalConstructor()->getMock();
        $this->event = $this->getMockBuilder('FOS\UserBundle\Event\ManualLoginEvent')->disableOriginalConstructor()->getMock();
        $this->user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->disableOriginalConstructor()->getMock();
        $this->response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->disableOriginalConstructor()->getMock();
        $this->firewall = "firewall";
        
        $this->manager = new ManualLoginManager($this->loginManager);
    }
    
    public function testOnManualLogin()
    {
        $this->event->expects($this->once())->method('getFirewall')->will($this->returnValue($this->firewall));
        $this->event->expects($this->once())->method('getUser')->will($this->returnValue($this->user));
        $this->event->expects($this->once())->method('getResponse')->will($this->returnValue(null));
        $this->loginManager->expects($this->once())->method('loginUser')->with($this->firewall, $this->user, null)->will($this->returnValue(null));
        $this->event->expects($this->never())->method('stopPropagation');
        
        $this->manager->onFosUserManualLogin($this->event);
    }
    
    public function testOnManualLoginException()
    {
        $this->event->expects($this->once())->method('getFirewall')->will($this->returnValue($this->firewall));
        $this->event->expects($this->once())->method('getUser')->will($this->returnValue($this->user));
        $this->event->expects($this->once())->method('getResponse')->will($this->returnValue(null));
        
        $this->loginManager->expects($this->once())->method('loginUser')
                ->with($this->firewall, $this->user, null)
                ->will($this->throwException($this->getMock('Symfony\Component\Security\Core\Exception\AccountStatusException', null, array(), '', false)));
        
        $this->event->expects($this->once())->method('stopPropagation');
        
        $this->manager->onFosUserManualLogin($this->event);
    }
}
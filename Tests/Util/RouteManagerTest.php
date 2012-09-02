<?php

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Util\RouteManager;

class RouteManagerTest extends \PHPUnit_Framework_TestCase
{    
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->disableOriginalConstructor()->getMock();
        $this->event = $this->getMockBuilder('FOS\UserBundle\Event\RouteEvent')->disableOriginalConstructor()->getMock();
        $this->manager = new RouteManager($this->container);
    }
    
    public function testOnFosUserRegistrationSuccessWithConfirm()
    {
        $this->container->expects($this->once())->method('getParameter')->with('fos_user.registration.confirmation.enabled')->will($this->returnValue(true));
        $this->event->expects($this->once())->method('setRoute')->with('fos_user_registration_check_email');
        $this->manager->onFosUserRegistrationSuccess($this->event);
    }
    
    public function testOnFosUserRegistrationSuccessWithoutConfirm()
    {
        $this->container->expects($this->once())->method('getParameter')->with('fos_user.registration.confirmation.enabled')->will($this->returnValue(false));
        $this->event->expects($this->once())->method('setRoute')->with('fos_user_registration_confirmed');
        $this->manager->onFosUserRegistrationSuccess($this->event);
    }
}
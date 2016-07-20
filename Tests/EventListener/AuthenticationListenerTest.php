<?php
namespace FOS\UserBundle\Tests\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\EventListener\AuthenticationListener;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthenticationListenerTest extends \PHPUnit_Framework_TestCase
{
    const FIREWALL_NAME = 'foo';

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var FilterUserResponseEvent */
    private $event;

    /** @var AuthenticationListener */
    private $listener;

    public function setUp()
    {
        $user = $this->getMock('FOS\UserBundle\Model\UserInterface');
        $user
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->event = new FilterUserResponseEvent($user, $request, $response);

        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $loginManager = $this->getMock('FOS\UserBundle\Security\LoginManagerInterface');

        $this->listener = new AuthenticationListener($loginManager, self::FIREWALL_NAME);
    }

    /**
     * @group legacy
     */
    public function testAuthenticateLegacy()
    {
        if (!method_exists($this->event, 'setDispatcher')) {
            $this->markTestSkipped('Legacy test which requires Symfony <3.0.');
        }

        $this->event->setDispatcher($this->eventDispatcher);
        $this->event->setName(FOSUserEvents::REGISTRATION_COMPLETED);

        $this->listener->authenticate($this->event);
    }

    public function testAuthenticate()
    {
        $this->listener->authenticate($this->event, FOSUserEvents::REGISTRATION_COMPLETED, $this->eventDispatcher);
    }
}

<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\EventListener\AuthenticationListener;
use FOS\UserBundle\FOSUserEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AuthenticationListenerTest extends TestCase
{
    const FIREWALL_NAME = 'foo';

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var FilterUserResponseEvent */
    private $event;

    /** @var AuthenticationListener */
    private $listener;

    public function setUp(): void
    {
        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->getMock();

        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->getMock();
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->event = new FilterUserResponseEvent($user, $request, $response);

        $this->eventDispatcher = $this->getMockBuilder('Symfony\Contracts\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $loginManager = $this->getMockBuilder('FOS\UserBundle\Security\LoginManagerInterface')->getMock();

        $this->listener = new AuthenticationListener($loginManager, self::FIREWALL_NAME);
    }

    public function testAuthenticate()
    {
        $this->listener->authenticate($this->event, FOSUserEvents::REGISTRATION_COMPLETED, $this->eventDispatcher);
    }
}

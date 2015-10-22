<?php

namespace FOS\UserBundle\Tests\EventListener;

use FOS\UserBundle\EventListener\RedirectListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RedirectListenerTest extends \PHPUnit_Framework_TestCase
{
    const ROUTE_NAME = 'foo';
    const ROUTE_URL = 'http://www.example.com';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RedirectListener
     */
    private $listener;

    public function setUp()
    {
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(self::ROUTE_NAME)
            ->willReturn(self::ROUTE_URL);

        $this->listener = new RedirectListener($this->router, self::ROUTE_NAME);
    }

    public function testRedirect()
    {
        $event = $this->getMockBuilder('FOS\UserBundle\Event\FormEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with(new RedirectResponse(self::ROUTE_URL));

        $this->listener->redirect($event);
    }
}

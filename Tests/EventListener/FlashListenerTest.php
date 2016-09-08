<?php
namespace FOS\UserBundle\Tests\EventListener;

use FOS\UserBundle\EventListener\FlashListener;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\Event;

class FlashListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Event */
    private $event;

    /** @var FlashListener */
    private $listener;

    public function setUp()
    {
        $this->event = new Event();

        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBag');

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');
        $session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag);

        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');

        $this->listener = new FlashListener($session, $translator);
    }

    /**
     * @group legacy
     */
    public function testAddSuccessFlashLegacy()
    {
        if (!method_exists($this->event, 'setDispatcher')) {
            $this->markTestSkipped('Legacy test which requires Symfony <3.0.');
        }

        $this->event->setName(FOSUserEvents::CHANGE_PASSWORD_COMPLETED);

        $this->listener->addSuccessFlash($this->event);
    }

    public function testAddSuccessFlash()
    {
        $this->listener->addSuccessFlash($this->event, FOSUserEvents::CHANGE_PASSWORD_COMPLETED);
    }
}

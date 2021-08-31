<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;
use TypeError;

/**
 * @internal
 * @final
 */
class FlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static $successMessages = [
        FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        FOSUserEvents::GROUP_CREATE_COMPLETED => 'group.flash.created',
        FOSUserEvents::GROUP_DELETE_COMPLETED => 'group.flash.deleted',
        FOSUserEvents::GROUP_EDIT_COMPLETED => 'group.flash.updated',
        FOSUserEvents::PROFILE_EDIT_COMPLETED => 'profile.flash.updated',
        FOSUserEvents::REGISTRATION_COMPLETED => 'registration.flash.user_created',
        FOSUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
    ];

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FlashListener constructor.
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_CREATE_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_DELETE_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_EDIT_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::REGISTRATION_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
        ];
    }

    /**
     * @param string $eventName
     */
    public function addSuccessFlash(/* \Symfony\Contracts\EventDispatcher\Event */ $event, $eventName)
    {
        if ($event instanceof LegacyEvent) {
            @trigger_error(sprintf('Passing an instance of "%s" is deprecated since version 2.3 and will be removed in 3.0.', LegacyEvent::class), E_USER_DEPRECATED);
        } elseif (!$event instanceof Event) {
            throw new TypeError(sprintf('Argument 2 passed to %s::addSuccessFlash must be an instance of %s, %s given.', self::class, Event::class, get_debug_type($event)));
        }

        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    /**
     * @param string$message
     *
     * @return string
     */
    private function trans($message, array $params = [])
    {
        return $this->translator->trans($message, $params, 'FOSUserBundle');
    }
}

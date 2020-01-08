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
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
    public function addSuccessFlash(Event $event, $eventName)
    {
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

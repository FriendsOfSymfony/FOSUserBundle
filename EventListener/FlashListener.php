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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class FlashListener implements EventSubscriberInterface
{
    private static $successMessages = array(
        FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        FOSUserEvents::CHANGE_PASSWORD_ERROR => 'change_password.flash.error',
        FOSUserEvents::GROUP_CREATE_COMPLETED => 'group.flash.created',
        FOSUserEvents::GROUP_CREATE_ERROR => 'group.flash.create_error',
        FOSUserEvents::GROUP_DELETE_COMPLETED => 'group.flash.deleted',
        FOSUserEvents::GROUP_EDIT_COMPLETED => 'group.flash.updated',
        FOSUserEvents::GROUP_EDIT_ERROR => 'group.flash.update_error',
        FOSUserEvents::PROFILE_EDIT_COMPLETED => 'profile.flash.updated',
        FOSUserEvents::PROFILE_EDIT_ERROR => 'profile.flash.update_error',
        FOSUserEvents::REGISTRATION_COMPLETED => 'registration.flash.user_created',
        FOSUserEvents::REGISTRATION_ERROR => 'registration.flash.user_create_error',
        FOSUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
        FOSUserEvents::RESETTING_RESET_ERROR => 'resetting.flash.error',
    );

    private $session;
    private $translator;

    public function __construct(Session $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::CHANGE_PASSWORD_ERROR => 'addErrorFlash',
            FOSUserEvents::GROUP_CREATE_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_CREATE_ERROR => 'addErrorFlash',
            FOSUserEvents::GROUP_DELETE_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_EDIT_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_EDIT_ERROR => 'addErrorFlash',
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::PROFILE_EDIT_ERROR => 'addErrorFlash',
            FOSUserEvents::REGISTRATION_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::REGISTRATION_ERROR => 'addErrorFlash',
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::RESETTING_RESET_ERROR => 'addErrorFlash',
        );
    }

    public function addSuccessFlash(Event $event)
    {
        $this->createMessage($event, 'success');
    }

    public function addErrorFlash(Event $event)
    {
        $this->createMessage($event, 'error');
    }

    private function createMessage(Event $event, $type)
    {
        if (!isset(self::$successMessages[$event->getName()])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add($type, $this->trans(self::$successMessages[$event->getName()]));
    }

    private function trans($message, array $params = array())
    {
        return $this->translator->trans($message, $params, 'FOSUserBundle');
    }
}

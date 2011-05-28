<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Events;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Listener to update the canonical fields when binding the form
 */
class UpdateCanonicalFieldsListener implements EventSubscriberInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents()
    {
        return Events::postBind;
    }

    public function postBind(DataEvent $event)
    {
        $user = $event->getForm()->getData();
        if ($user instanceof UserInterface) {
            $this->userManager->updateCanonicalFields($user);
        }
    }
}

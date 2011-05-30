<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\FormEvents;
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
        return array(FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(DataEvent $event)
    {
        $user = $event->getForm()->getData();
        if ($user instanceof UserInterface) {
            $this->userManager->updateCanonicalFields($user);
        }
    }
}

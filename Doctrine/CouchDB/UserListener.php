<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Doctrine\CouchDB;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\CouchDB\Event;
use Doctrine\ODM\CouchDB\Event\LifecycleEventArgs;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserListener implements EventSubscriber
{
    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface
     */
    private $userManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return array(
            Event::prePersist,
            Event::preUpdate,
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getDocument();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getDocument();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * Updates the user properties.
     *
     * @param UserInterface $user
     */
    private function updateUserFields(UserInterface $user)
    {
        if (null === $this->userManager) {
            $this->userManager = $this->container->get('fos_user.user_manager');
        }

        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
    }
}

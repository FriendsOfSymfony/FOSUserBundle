<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Base Doctrine listener updating the canonical username and password fields.
 *
 * Overwritten by database specific listeners to register the right events and
 * to let the UoW recalculate the change set if needed.
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author David Buchmann <mail@davidbu.ch>
 */
abstract class AbstractUserListener implements EventSubscriber
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

    /**
     * Pre persist listener based on doctrine commons, overwrite for drivers
     * that are not compatible with the commons events.
     *
     * @param LifecycleEventArgs $args weak typed to allow overwriting
     */
    public function prePersist($args)
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * Pre update listener based on doctrine commons, overwrite to update
     * the changeset in the UoW and to handle non-common event argument
     * class.
     *
     * @param LifecycleEventArgs $args weak typed to allow overwriting
     */
    public function preUpdate($args)
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * This must be called on prePersist and preUpdate if the event is about a
     * user.
     *
     * @param UserInterface $user
     */
    protected function updateUserFields(UserInterface $user)
    {
        if (null === $this->userManager) {
            $this->userManager = $this->container->get('fos_user.user_manager');
        }

        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
    }
}

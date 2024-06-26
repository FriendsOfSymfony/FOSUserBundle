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
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;

/**
 * Doctrine listener updating the canonical username and password fields.
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author David Buchmann <mail@davidbu.ch>
 *
 * @internal
 */
final class UserListener implements EventSubscriber
{
    private $passwordUpdater;
    private $canonicalFieldsUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * Pre persist listener based on doctrine common.
     *
     * @param LifecycleEventArgs<ObjectManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * Pre update listener based on doctrine common.
     *
     * @param LifecycleEventArgs<ObjectManager> $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
            $this->recomputeChangeSet($args->getObjectManager(), $object);
        }
    }

    /**
     * Updates the user properties.
     */
    private function updateUserFields(UserInterface $user): void
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->passwordUpdater->hashPassword($user);
    }

    /**
     * Recomputes change set for Doctrine implementations not doing it automatically after the event.
     */
    private function recomputeChangeSet(ObjectManager $om, UserInterface $user): void
    {
        if ($om instanceof EntityManager) {
            $meta = $om->getClassMetadata(get_class($user));
            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);

            return;
        }

        if ($om instanceof DocumentManager) {
            $meta = $om->getClassMetadata(get_class($user));
            $om->getUnitOfWork()->recomputeSingleDocumentChangeSet($meta, $user);
        }
    }
}

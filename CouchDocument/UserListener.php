<?php

namespace FOS\UserBundle\CouchDocument;

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

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    private function handleEvent(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();
        if ($document instanceof UserInterface) {
            if (null === $this->userManager) {
                $this->userManager = $this->container->get('fos_user.user_manager');
            }
            $this->userManager->updateCanonicalFields($document);
            $this->userManager->updatePassword($document);
            if ($args instanceof PreUpdateEventArgs) {
                // We are doing a update, so we must force Doctrine to update the
                // changeset in case we changed something above
                $dm   = $args->getDocumentManager();
                $uow  = $dm->getUnitOfWork();
                $meta = $dm->getClassMetadata(get_class($entity));
                $uow->recomputeSingleEntityChangeSet($meta, $entity);
            }
        }
    }
}

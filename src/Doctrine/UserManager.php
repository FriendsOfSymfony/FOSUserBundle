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

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;

class UserManager extends BaseUserManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     *
     * @phpstan-var class-string<UserInterface>
     */
    private $class;

    /**
     * Constructor.
     *
     * @phpstan-param class-string<UserInterface> $class
     */
    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater, ObjectManager $om, string $class)
    {
        parent::__construct($passwordUpdater, $canonicalFieldsUpdater);

        $this->objectManager = $om;
        $this->class = $class;
    }

    public function deleteUser(UserInterface $user): void
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush();
    }

    /**
     * @phpstan-return class-string<UserInterface>
     */
    public function getClass(): string
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * @return iterable<UserInterface>
     */
    public function findUsers(): iterable
    {
        return $this->getRepository()->findAll();
    }

    public function reloadUser(UserInterface $user): void
    {
        $this->objectManager->refresh($user);
    }

    public function updateUser(UserInterface $user, bool $andFlush = true): void
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * @return ObjectRepository<UserInterface>
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->objectManager->getRepository($this->getClass());
    }
}

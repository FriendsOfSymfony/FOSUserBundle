<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Model;

use FOS\UserBundle\Events;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\UserPersistEvent;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Abstract User Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class UserManager implements UserManagerInterface, UserProviderInterface
{
    protected $encoderFactory;
    protected $usernameCanonicalizer;
    protected $emailCanonicalizer;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer)
    {
        $this->encoderFactory = $encoderFactory;
        $this->usernameCanonicalizer = $usernameCanonicalizer;
        $this->emailCanonicalizer = $emailCanonicalizer;
    }

    /**
     * Sets the event dispatcher used by the manager.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns an empty user instance
     *
     * @return UserInterface
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class;

        if (null !== $this->dispatcher) {
            $event = new UserEvent($user);
            $this->dispatcher->dispatch(Events::USER_CREATE, $event);

            return $event->getUser();
        }

        return $user;
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    abstract protected function doUpdateUser(UserInterface $user, $andFlush = true);

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        if (null !== $this->dispatcher) {
            $event = new UserPersistEvent($user);
            $this->dispatcher->dispatch(Events::USER_PRE_PERSIST, $event);

            if ($event->isPersistenceAborted()) {
                return;
            }
        }

        $this->doUpdateUser($user, $andFlush);

        if (null !== $this->dispatcher) {
            $event = new UserEvent($user);
            $this->dispatcher->dispatch(Events::USER_POST_PERSIST, $event);
        }
    }

    /**
     * This function is to be implemented and perform the actual deletion
     * of the user object.
     *
     * @param UserInterface $user
     * @param bool $andFlush
     */
    abstract protected function doDeleteUser(UserInterface $user);

    /**
     * Deletes a user.
     *
     * @param UserInterface $user
     * @param Boolean $andFlush
     * @return void
     */
    public function deleteUser(UserInterface $user)
    {
        if (null !== $this->dispatcher) {
            $event = new UserPersistEvent($user);
            $this->dispatcher->dispatch(Events::USER_PRE_DELETE, $event);

            if ($event->isPersistenceAborted()) {
                return;
            }
        }

        $this->doDeleteUser($user);

        if (null !== $this->dispatcher) {
            $event = new UserEvent($user);
            $this->dispatcher->dispatch(Events::USER_POST_DELETE, $event);
        }
    }

    /**
     * Finds a user by email
     *
     * @param string $email
     * @return UserInterface
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('emailCanonical' => $this->canonicalizeEmail($email)));
    }

    /**
     * Finds a user by username
     *
     * @param string $username
     * @return UserInterface
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(array('usernameCanonical' => $this->canonicalizeUsername($username)));
    }

    /**
     * Finds a user either by email, or username
     *
     * @param string $usernameOrEmail
     * @return UserInterface
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * Finds a user either by confirmation token
     *
     * @param string $token
     * @return UserInterface
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(array('confirmationToken' => $token));
    }

    /**
     * Refreshed a user by User Instance
     *
     * Throws UnsupportedUserException if a User Instance is given which is not
     * managed by this UserManager (so another Manager could try managing it)
     *
     * It is strongly discouraged to use this method manually as it bypasses
     * all ACL checks.
     *
     * @param SecurityUserInterface $user
     * @return UserInterface
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        $class = $this->getClass();
        if (!$user instanceof $class) {
            throw new UnsupportedUserException('Account is not supported.');
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Loads a user by username
     *
     * It is strongly discouraged to call this method manually as it bypasses
     * all ACL checks.
     *
     * @param string $username
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function updateCanonicalFields(UserInterface $user)
    {
        $user->setUsernameCanonical($this->canonicalizeUsername($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    /**
     * Canonicalizes an email
     *
     * @param string $email
     * @return string
     */
    protected function canonicalizeEmail($email)
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }

    /**
     * Canonicalizes a username
     *
     * @param string $username
     * @return string
     */
    protected function canonicalizeUsername($username)
    {
        return $this->usernameCanonicalizer->canonicalize($username);
    }

    protected function getEncoder(UserInterface $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->getClass();
    }
}

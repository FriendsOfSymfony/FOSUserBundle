<?php

namespace Bundle\FOS\UserBundle\Model;

use Symfony\Component\Security\Exception\UnsupportedAccountException;
use Symfony\Component\Security\Exception\UsernameNotFoundException;
use Symfony\Component\Security\User\AccountInterface;
use Symfony\Component\Security\User\UserProviderInterface;

/**
 * Abstract User Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class UserManager implements UserManagerInterface, UserProviderInterface
{
    protected $encoderFactory;
    protected $algorithm;

    public function __construct($encoderFactory, $algorithm)
    {
        $this->encoderFactory = $encoderFactory;
        $this->algorithm = $algorithm;
    }

    /**
     * Returns an empty user instance
     *
     * @return User
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class;
        $user->setAlgorithm($this->algorithm);

        return $user;
    }

    /**
     * Finds a user by email
     *
     * @param string $email
     * @return User
     */
    public function findUserByEmail($email)
    {
        $user = $this->createUser();
        $user->setEmail($email);
        return $this->findUserBy(array('emailCanonical' => $user->getEmailCanonical()));
    }

    /**
     * Finds a user by username
     *
     * @param string $username
     * @return User
     */
    public function findUserByUsername($username)
    {
        $user = $this->createUser();
        $user->setUsername($username);
        return $this->findUserBy(array('usernameCanonical' => $user->getUsernameCanonical()));
    }

    /**
     * Finds a user by its display name
     *
     * @param string $displayName
     * @return User
     */
    public function findUserByDisplayName($displayName)
    {
        return $this->findUserByUsername($displayName);
    }

    /**
     * Finds a user either by email, or username
     *
     * @param string $usernameOrEmail
     * @return User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(array('confirmationToken' => $token));
    }

    /**
     * Finds a user by account
     *
     * It is strongly discouraged to use this method manually as it bypasses
     * all ACL checks.
     *
     * @param AccountInterface $user
     * @return User
     */
    public function loadUserByAccount(AccountInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedAccountException('Account is not supported.');
        }

        return $this->loadUserByUsername((string) $user);
    }

    /**
     * Loads a user by username
     *
     * It is strongly discouraged to call this method manually as it bypasses
     * all ACL checks.
     *
     * @RunAs(roles="ROLE_SUPERADMIN")
     * @param string $username
     * @return AccountInterface
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
    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $user->setAlgorithm($this->algorithm);
            $encoder = $this->encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }
}

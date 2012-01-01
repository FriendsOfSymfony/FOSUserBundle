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

use Symfony\Component\Validator\Constraint;

/**
 * Interface to be implemented by user managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to users should happen through this interface.
 *
 * The class also contains ACL annotations which will only work if you have the
 * SecurityExtraBundle installed, otherwise they will simply be ignored.
 *
 * @author Gordon Franke <info@nevalon.de>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface UserManagerInterface
{
    /**
     * Creates an empty user instance.
     *
     * @return UserInterface
     */
    function createUser();

    /**
     * Deletes a user.
     *
     * @param UserInterface $user
     */
    function deleteUser(UserInterface $user);

    /**
     * Finds one user by the given criteria.
     *
     * @param array $criteria
     * @return UserInterface
     */
    function findUserBy(array $criteria);

    /**
     * Find a user by its username.
     *
     * @param string  $username
     * @return UserInterface or null if user does not exist
     */
    function findUserByUsername($username);

    /**
     * Finds a user by its email.
     *
     * @param string  $email
     * @return UserInterface or null if user does not exist
     */
    function findUserByEmail($email);

    /**
     * Finds a user by its username or email.
     *
     * @param string  $usernameOrEmail
     * @return UserInterface or null if user does not exist
     */
    function findUserByUsernameOrEmail($usernameOrEmail);

    /**
     * Finds a user by its confirmationToken.
     *
     * @param string  $token
     * @return UserInterface or null if user does not exist
     */
    function findUserByConfirmationToken($token);

    /**
     * Returns a collection with all user instances.
     *
     * @return \Traversable
     */
    function findUsers();

    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    function getClass();

    /**
     * Reloads a user.
     *
     * @param UserInterface $user
     */
    function reloadUser(UserInterface $user);

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     */
    function updateUser(UserInterface $user);

    /**
     * Updates the canonical username and email fields for a user.
     *
     * @param UserInterface $user
     */
    function updateCanonicalFields(UserInterface $user);

    /**
     * Updates a user password if a plain password is set.
     *
     * @param UserInterface $user
     */
    function updatePassword(UserInterface $user);

    /**
     * Checks the uniqueness of the given fields, returns true if its unique.
     *
     * @param UserInterface $value
     * @param Constraint    $constraint
     * @return Boolean
     */
    function validateUnique(UserInterface $value, Constraint $constraint);
}

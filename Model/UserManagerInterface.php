<?php

/**
 * (c) Gordon Franke <info@nevalon.de>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
     * Creates an empty user instance
     *
     * @return User
     */
    function createUser();

    /**
     * Deletes a user
     *
     * @SecureParam(name="user", permissions="DELETE")
     * @param User $user
     * @return void
     */
    function deleteUser(UserInterface $user);

    /**
     * Finds one user by the given criteria
     *
     * @SecureReturn(permissions="VIEW")
     * @param array $criteria
     * @return User
     */
    function findUserBy(array $criteria);

    /**
     * Find a user by its username
     * @param   string  $username
     * @return  User or null if user does not exist
     */
    function findUserByUsername($username);

    /**
     * Find a user by its email
     * @param   string  $email
     * @return  User or null if user does not exist
     */
    function findUserByEmail($email);

    /**
     * Find a user by its username or email
     * @param   string  $usernameOrEmail
     * @return  User or null if user does not exist
     */
    function findUserByUsernameOrEmail($usernameOrEmail);

    /**
     * Find a user by its confirmationToken
     * @param   string  $token
     * @return  User or null if user does not exist
     */
    function findUserByConfirmationToken($token);

    /**
     * Returns a collection with all user instances
     *
     * @return Traversable
     */
    function findUsers();

    /**
     * Returns the user's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a user
     *
     * @SecureParam(name="user", permissions="EDIT")
     * @param User $user
     * @return void
     */
    function updateUser(UserInterface $user);

    /**
     * Updates the canonical username and email fields for a user
     *
     * @SecureParam(name="user", permissions="EDIT")
     * @param User $user
     * @return void
     */
    function updateCanonicalFields(UserInterface $user);

    /**
     * Updates a user password if a plain password is set
     *
     * @SecureParam(name="user", permissions="EDIT")
     * @param User $user
     * @return void
     */
    function updatePassword(UserInterface $user);

    /**
     * Checks the uniqueness of the given fields
     *
     * @param UserInterface|string $value
     * @param Constraint $constraint
     * @return boolean
     */
    function validateUnique($value, Constraint $constraint);
}

<?php

namespace FOS\UserBundle\Util;


/**
 * Executes some manipulations on the users.
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author Luis Cordova <cordoval@gmail.com>
 */
interface UserManipulatorInterface
{
    /**
     * Creates a user and returns it.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param bool $active
     * @param bool $superadmin
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function create($username, $password, $email, $active, $superadmin);

    /**
     * Activates the given user.
     *
     * @param string $username
     */
    public function activate($username);

    /**
     * Deactivates the given user.
     *
     * @param string $username
     */
    public function deactivate($username);

    /**
     * Changes the password for the given user.
     *
     * @param string $username
     * @param string $password
     */
    public function changePassword($username, $password);

    /**
     * Promotes the given user.
     *
     * @param string $username
     */
    public function promote($username);

    /**
     * Demotes the given user.
     *
     * @param string $username
     */
    public function demote($username);

    /**
     * Adds role to the given user.
     *
     * @param string $username
     * @param string $role
     *
     * @return bool true if role was added, false if user already had the role
     */
    public function addRole($username, $role);

    /**
     * Removes role from the given user.
     *
     * @param string $username
     * @param string $role
     *
     * @return bool true if role was removed, false if user didn't have the role
     */
    public function removeRole($username, $role);
}

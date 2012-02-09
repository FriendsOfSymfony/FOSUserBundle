<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Util;

use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Interface for execution of some manipulations on the users
 *
 * @author Alexander Miehe <alexander.miehe@gmx.de>
 */
interface UserManipulatorInterface
{

    /**
     * Creates a user and returns it.
     *
     * @param string  $username
     * @param string  $password
     * @param string  $email
     * @param Boolean $active
     * @param Boolean $superadmin
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
     * @return Boolean true if role was added, false if user already had the role
     */
    public function addRole($username, $role);

    /**
     * Removes role from the given user.
     *
     * @param string $username
     * @param string $role
     *
     * @return Boolean true if role was removed, false if user didn't have the role
     */
    public function removeRole($username, $role);
}

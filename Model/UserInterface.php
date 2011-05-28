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

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface UserInterface extends AdvancedUserInterface
{
    function addRole($role);

    function getAlgorithm();

    function setAlgorithm($algorithm);

    function setUsername($username);

    function getUsernameCanonical();

    function setUsernameCanonical($usernameCanonical);

    function getEmail();

    function setEmail($email);

    function getEmailCanonical();

    function setEmailCanonical($emailCanonical);

    function getPlainPassword();

    function setPlainPassword($password);

    function setPassword($password);

    function isSuperAdmin();

    function isUser(UserInterface $user = null);

    function setEnabled($boolean);

    function setSuperAdmin($boolean);

    function getConfirmationToken();

    function setConfirmationToken($confirmationToken);

    function hasRole($role);

    function setRoles(array $roles);

    function removeRole($role);

    function getGroups();

    function getGroupNames();

    function hasGroup($name);

    function addGroup(GroupInterface $group);

    function removeGroup(GroupInterface $group);
}

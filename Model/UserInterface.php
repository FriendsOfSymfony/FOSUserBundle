<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Model;

use Symfony\Component\Security\User\AdvancedAccountInterface;

interface UserInterface extends AdvancedAccountInterface
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

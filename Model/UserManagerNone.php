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

/**
 * Fallback User Manager implementation when db_driver is not configured.
 *
 * @author Andrey F. Mindubaev <andrey@mindubaev.ru>
 */
class UserManagerNone extends UserManager
{
    public function deleteUser(UserInterface $user)
    {
        throw new \RuntimeException('The child node "db_driver" at path "fos_user" must be configured.');
    }

    public function findUserBy(array $criteria)
    {
        throw new \RuntimeException('The child node "db_driver" at path "fos_user" must be configured.');
    }

    public function findUsers()
    {
        throw new \RuntimeException('The child node "db_driver" at path "fos_user" must be configured.');
    }

    public function getClass()
    {
        throw new \RuntimeException('The child node "db_driver" at path "fos_user" must be configured.');
    }

    public function reloadUser(UserInterface $user)
    {
        throw new \RuntimeException('The child node "db_driver" at path "fos_user" must be configured.');
    }

    public function updateUser(UserInterface $user)
    {
        throw new \RuntimeException('The child node "db_driver" at path "fos_user" must be configured.');
    }
}

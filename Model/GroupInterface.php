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
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupInterface
{
    /**
     * @return Group
     */
    public function addRole($role);

    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return boolean
     */
    public function hasRole($role);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @return Group
     */
    public function removeRole($role);

    /**
     * @param string $name
     *
     * @return Group
     */
    public function setName($name);

    /**
     * @return Group
     */
    public function setRoles(array $roles);
}

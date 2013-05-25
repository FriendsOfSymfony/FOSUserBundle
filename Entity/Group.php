<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * @deprecated directly extend the classes in the Model namespace
 */
class Group extends BaseGroup
{
    public function __construct($name, $roles = array())
    {
        // you should extend the class in the Model namespace directly
        trigger_error(E_USER_DEPRECATED);
        parent::__construct($name, $roles);
    }
}

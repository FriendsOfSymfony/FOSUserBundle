<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Document;

use FOS\UserBundle\Model\User as AbstractUser;

/**
 * @deprecated directly extend the classes in the Model namespace
 */
abstract class User extends AbstractUser
{
    public function __construct()
    {
        // you should extend the class in the Model namespace directly
        trigger_error(E_USER_DEPRECATED);
        parent::__construct();
    }
}

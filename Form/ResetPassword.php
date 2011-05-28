<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form;

use FOS\UserBundle\Model\UserInterface;

class ResetPassword
{
    /**
     * User whose password is changed
     *
     * @var UserInterface
     */
    public $user;

    /**
     * @var string
     */
    public $new;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }
}

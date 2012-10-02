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

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\GroupManager as BaseGroupManager;

/**
 * BC class for people extending it in their bundle.
 * TODO Remove this class on July 31st
 */
class GroupManager extends BaseGroupManager
{
    protected $em;

    public function __construct(EntityManager $em, $class)
    {
        parent::__construct($em, $class);
        $this->em = $em;
    }
}

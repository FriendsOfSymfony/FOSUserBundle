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
 * @author Ben Glassman <ben@vtdesignworks.com>
 */
interface FullNameInterface
{
    /**
     * Get the user's full name
     *
     * @return self
     */
    public function getFullName();
}

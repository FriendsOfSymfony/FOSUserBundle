<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\Model\Group;

class TestGroup extends Group
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}

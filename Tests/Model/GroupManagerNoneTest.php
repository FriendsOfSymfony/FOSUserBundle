<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Model;

use FOS\UserBundle\Model\GroupManagerNone;
use PHPUnit\Framework\TestCase;

class GroupManagerNoneTest extends TestCase
{
    /**
     * @dataProvider methodsProvider
     * @expectedException \RuntimeException
     */
    public function testMethods($name, $arguments)
    {
        $manager = new GroupManagerNone();

        call_user_func_array(array($manager, $name), $arguments);
    }

    public function methodsProvider()
    {
        $group = $this->getMockBuilder('FOS\UserBundle\Model\GroupInterface')->getMock();

        return array(
            array('deleteGroup', array($group)),
            array('findGroupBy', array(array('id' => 1))),
            array('findGroups', array()),
            array('getClass', array()),
            array('updateGroup', array($group)),
        );
    }
}

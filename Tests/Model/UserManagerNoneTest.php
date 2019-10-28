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

use FOS\UserBundle\Model\UserManagerNone;
use PHPUnit\Framework\TestCase;

class UserManagerNoneTest extends TestCase
{
    /**
     * @dataProvider methodsProvider
     * @expectedException \RuntimeException
     */
    public function testMethods($name, $arguments)
    {
        /** @var \FOS\UserBundle\Util\PasswordUpdaterInterface $passwordUpdater */
        $passwordUpdater = $this->getMockBuilder('FOS\UserBundle\Util\PasswordUpdaterInterface')->getMock();
        /** @var \FOS\UserBundle\Util\CanonicalFieldsUpdater $fieldsUpdater */
        $fieldsUpdater = $this->getMockBuilder('FOS\UserBundle\Util\CanonicalFieldsUpdater')
            ->disableOriginalConstructor()
            ->getMock();

        $manager = new UserManagerNone($passwordUpdater, $fieldsUpdater);

        call_user_func_array(array($manager, $name), $arguments);
    }

    public function methodsProvider()
    {
        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->getMock();

        return array(
            array('deleteUser', array($user)),
            array('findUserBy', array(array('id' => 1))),
            array('findUsers', array()),
            array('getClass', array()),
            array('reloadUser', array($user)),
            array('updateUser', array($user)),
        );
    }
}

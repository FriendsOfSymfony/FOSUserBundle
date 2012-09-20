<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Propel;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Propel')) {
            $this->markTestSkipped('Propel not installed');
        }
    }

    public function testSerialize()
    {
        $group = new Group();
        $group->setName('Developers');

        $user = new User();
        $user->setEmail('foobar@example.com');
        $user->setPassword('123456');
        $user->addGroup($group);
        $user->save();

        $userId = $user->getId();
        $this->assertInternalType('int', $userId);

        $serialized = serialize($user);
        UserPeer::clearInstancePool();
        $this->assertCount(0, UserPeer::$instances);

        $unserialized = unserialize($serialized);
        $fetchedUser = UserQuery::create()->findOneById($userId);

        $this->assertInstanceOf('FOS\UserBundle\Propel\User', $unserialized);
        $this->assertCount(1, UserPeer::$instances);
        $this->assertTrue($fetchedUser->equals($unserialized));

        $this->assertCount(1, $unserialized->getGroups());
    }
}

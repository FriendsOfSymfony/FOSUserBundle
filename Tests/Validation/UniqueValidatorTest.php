<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Validation;

use FOS\UserBundle\Validator\UniqueValidator;
use FOS\UserBundle\Validator\Unique;

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $validator;
    private $userManagerMock;
    private $constraint;

    public function setUp()
    {
        $this->userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $this->constraint = new Unique();
        $this->validator = new UniqueValidator($this->userManagerMock);
    }

    public function testFalseOnDuplicateUserProperty()
    {
        $this->userManagerMock->expects($this->once())
                ->method('validateUnique')
                ->will($this->returnValue(false))
                ->with($this->equalTo('propertyValue'), $this->equalTo($this->constraint));

        $this->assertFalse($this->validator->isValid('propertyValue', $this->constraint));
    }

    public function testTrueOnUniqueUserProperty()
    {
        $this->userManagerMock->expects($this->once())
                ->method('validateUnique')
                ->will($this->returnValue(true))
                ->with($this->equalTo('propertyValue'), $this->equalTo($this->constraint));

        $this->assertTrue($this->validator->isValid('propertyValue', $this->constraint));
    }
}

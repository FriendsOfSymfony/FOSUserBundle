<?php

/*
 * This file is part of the Symfony FOSUB
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace FOS\UserBundle\Tests\Validation;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Validator\UniqueValidator;
use FOS\UserBundle\Validator\Unique;

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;
    protected $userManagerMock;
    protected $constraint;
    protected $userProperty;

    public function setUp()
    {
        $this->userManagerMock = $this->createUserManagerMock(array('validateUnique'));
        $this->constraintMock = $this->createUniqueConstraintMock(array());
        $this->validator = new UniqueValidator($this->userManagerMock);
        $this->userProperty = 'propertyValue';
    }

    public function testFalseOnDuplicateUserProperty()
    {
        $this->userManagerMock->expects($this->once())
                ->method('validateUnique')
                ->will($this->returnValue(false))
                ->with($this->equalTo($this->userProperty), $this->equalTo($this->constraintMock));

        $this->assertFalse($this->validator->isValid($this->userProperty, $this->constraintMock));
    }

    public function testTrueOnUniqueUserProperty()
    {
        $this->userManagerMock->expects($this->once())
                ->method('validateUnique')
                ->will($this->returnValue(true))
                ->with($this->equalTo($this->userProperty), $this->equalTo($this->constraintMock));

        $this->assertTrue($this->validator->isValid($this->userProperty, $this->constraintMock));
    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

    protected function createUniqueConstraintMock(array $methods)
    {
        $constraint = $this->getMockBuilder('FOS\UserBundle\Validator\Unique')
            ->disableOriginalConstructor()
            ->getMock();
        
        return $constraint;
    }
}

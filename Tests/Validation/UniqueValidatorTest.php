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
    private $user;

    public function setUp()
    {
        $options = array(
            'property' => 'username',
        );
        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
                ->disableOriginalConstructor()
                ->getMock();
        $this->constraint = new Unique($options);
        $this->userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $this->validator = new UniqueValidator($this->userManagerMock);
        $this->validator->initialize($context);
        $this->user = $this->getMock('FOS\UserBundle\Model\UserInterface');
    }

    public function testFalseOnDuplicateUserProperty()
    {
        $this->userManagerMock->expects($this->once())
                ->method('validateUnique')
                ->will($this->returnValue(false))
                ->with($this->equalTo($this->user), $this->equalTo($this->constraint));

        $this->assertFalse($this->validator->isValid($this->user, $this->constraint));
    }

    public function testTrueOnUniqueUserProperty()
    {
        $this->userManagerMock->expects($this->once())
                ->method('validateUnique')
                ->will($this->returnValue(true))
                ->with($this->equalTo($this->user), $this->equalTo($this->constraint));

        $this->assertTrue($this->validator->isValid($this->user, $this->constraint));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testBadType()
    {
        $this->validator->isValid('bad_type', $this->constraint);
    }
}

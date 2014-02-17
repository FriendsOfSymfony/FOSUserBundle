<?php

namespace FOS\UserBundle\Tests\Validator\Constraints;

use FOS\UserBundle\Form\Model\ResettingRequest;
use FOS\UserBundle\Validator\Constraints\UserExists;
use FOS\UserBundle\Validator\Constraints\UserExistsValidator;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserExistsValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidationWithInvalidIdentifier()
    {
        $invalidIdentifier = 'invalid_identifier';

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->will($this->throwException(new UsernameNotFoundException()))
            ->with($invalidIdentifier)
        ;

        $contextMock = $this->getMock('Symfony\Component\Validator\ExecutionContextInterface');
        $contextMock->expects($this->once())
            ->method('addViolation')
        ;

        $resettingRequest = new ResettingRequest();
        $resettingRequest->identifier = $invalidIdentifier;

        $userExistValidator = new UserExistsValidator($userProviderMock);
        $userExistValidator->initialize($contextMock);
        $userExistValidator->validate($resettingRequest, new UserExists());
    }
}

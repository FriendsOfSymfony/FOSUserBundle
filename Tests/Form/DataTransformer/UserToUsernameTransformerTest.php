<?php

namespace FOS\UserBundle\Tests\Form\DataTransformer;

use FOS\UserBundle\Form\DataTransformer\UserToUsernameTransformer;

/**
 * Description of UserToUsernameTransformerTest
 *
 * @author Bryan J. Agee <bryan@pamiris.com>
 */
class UserToUsernameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var UserToUsernameTransformer
     */
    private $transformer;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userManagerMock;
    
    protected function setUp()
    {
        $this->userManagerMock = $this->getMockBuilder('\FOS\UserBundle\Model\UserManager')
                ->disableOriginalConstructor()
                ->getMock();
        $this->transformer = new UserToUsernameTransformer($this->userManagerMock);
    }
    
    public function testTransformNullValue()
    {
        $this->assertNull($this->transformer->transform(null));
    }
    
    public function testReverseTransformNullValue()
    {
        $this->assertNull($this->transformer->reverseTransform(null));
        $this->assertNull($this->transformer->reverseTransform(''));
    }
    
    public function testExceptionThrownForNonUserInterface()
    {
        $this->setExpectedException('Symfony\Component\Form\Exception\UnexpectedTypeException');
        $this->transformer->transform(new \stdClass);
    }
    
    public function testExceptionThrownForNonStringUsername()
    {
        $this->setExpectedException('Symfony\Component\Form\Exception\UnexpectedTypeException');
        $this->transformer->reverseTransform(1224);
    }
    
    public function testTransform()
    {
        $username = 'anotherTestUser';
        
        $userMock = $this->getMock('FOS\UserBundle\Model\UserInterface');
        $userMock->expects($this->once())
                ->method('getUsername')
                ->willReturn($username);
        
        $this->assertEquals($username, $this->transformer->transform($userMock));
    }
    
    public function testReverseTransform()
    {
        $username = 'aTestUser';
        $this->userManagerMock->expects($this->once())
                ->method('findUserByUsername')
                ->with($username);
        $this->transformer->reverseTransform($username);
    }
}

<?php

namespace FOS\UserBundle\Tests\Validator;

use FOS\UserBundle\Validator\Initializer;

/**
 * Description of InitializerTest
 *
 * @author Bryan J. Agee <bryan@pamiris.com>
 */
class InitializerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testInitializeObject()
    {
        $userManager = $this->getMockBuilder('\FOS\UserBundle\Model\UserManager')
                ->disableOriginalConstructor()
                ->getMock();
        $initializer = new Initializer($userManager);

        $userManager->expects($this->once())
                ->method('updateCanonicalFields');
        
        $userMock = $this->getMock('FOS\UserBundle\Model\UserInterface');
        
        $initializer->initialize($userMock);
    }
}

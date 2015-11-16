<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\ResettingFormType;
use FOS\UserBundle\Tests\TestUser;

class ResettingFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();

        $form = $this->factory->create(ResettingFormType::class, $user);
        $formData = array(
            'plainPassword' => array(
                'first'         => 'test',
                'second'        => 'test',
            )
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());
        $this->assertEquals('test', $user->getPlainPassword());
    }
}

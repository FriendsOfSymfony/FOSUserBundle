<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\LegacyFormHelper;

class RegistrationFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();

        $form = $this->factory->create(LegacyFormHelper::getType('FOS\UserBundle\Form\Type\RegistrationFormType'), $user);
        $formData = array(
            'username'      => 'bar',
            'email'         => 'john@doe.com',
            'plainPassword' => array(
                'first'         => 'test',
                'second'        => 'test',
            )
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());
        $this->assertEquals('bar', $user->getUsername());
        $this->assertEquals('john@doe.com', $user->getEmail());
        $this->assertEquals('test', $user->getPlainPassword());
    }

    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new RegistrationFormType('FOS\UserBundle\Tests\TestUser'),
        ));
    }
}

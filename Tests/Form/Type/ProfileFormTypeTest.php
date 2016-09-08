<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType;
use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\LegacyFormHelper;

class ProfileFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();

        $form = $this->factory->create(LegacyFormHelper::getType('FOS\UserBundle\Form\Type\ProfileFormType'), $user);
        $formData = array(
            'username'      => 'bar',
            'email'         => 'john@doe.com',
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());
        $this->assertEquals('bar', $user->getUsername());
        $this->assertEquals('john@doe.com', $user->getEmail());
    }

    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new ProfileFormType('FOS\UserBundle\Tests\TestUser'),
        ));
    }
}

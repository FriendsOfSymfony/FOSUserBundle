<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\LegacyFormHelper;

class ChangePasswordFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();
        $user->setPassword('foo');

        $form = $this->factory->create(LegacyFormHelper::getType('FOS\UserBundle\Form\Type\ChangePasswordFormType'), $user);
        $formData = array(
            'current_password'      => 'foo',
            'plainPassword'         => array(
                'first'     => 'bar',
                'second'    => 'bar',
            ),
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());
        $this->assertEquals('bar', $user->getPlainPassword());
    }

    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new ChangePasswordFormType('FOS\UserBundle\Tests\TestUser'),
        ));
    }
}

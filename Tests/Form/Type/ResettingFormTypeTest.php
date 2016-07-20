<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\ResettingFormType;
use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\LegacyFormHelper;

class ResettingFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();

        $form = $this->factory->create(LegacyFormHelper::getType('FOS\UserBundle\Form\Type\ResettingFormType'), $user);
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

    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new ResettingFormType('FOS\UserBundle\Tests\TestUser'),
        ));
    }
}

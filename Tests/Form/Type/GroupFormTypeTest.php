<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\GroupFormType;
use FOS\UserBundle\Tests\TestGroup;
use FOS\UserBundle\Util\LegacyFormHelper;

class GroupFormTypeTest extends TypeTestCase
{
    public function testSubmit()
    {
        $group = new TestGroup('foo');

        $form = $this->factory->create(LegacyFormHelper::getType('FOS\UserBundle\Form\Type\GroupFormType'), $group);
        $formData = array(
            'name'      => 'bar',
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($group, $form->getData());
        $this->assertEquals('bar', $group->getName());
    }

    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new GroupFormType('FOS\UserBundle\Tests\TestGroup'),
        ));
    }
}

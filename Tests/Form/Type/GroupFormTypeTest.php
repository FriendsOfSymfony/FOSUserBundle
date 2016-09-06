<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\GroupFormType;
use FOS\UserBundle\Form\Type\RolesFormType;
use FOS\UserBundle\Tests\TestGroup;
use FOS\UserBundle\Util\LegacyFormHelper;
use FOS\UserBundle\Util\RolesHelper;
use Symfony\Component\Form\PreloadedExtension;

class GroupFormTypeTest extends TypeTestCase
{
    private $rolesHelper;

    protected function setUp()
    {
        $this->rolesHelper = new RolesHelper(array());

        parent::setUp();
    }

    protected function getExtensions()
    {
        $extensions = parent::getTypeExtensions();

        $type = new RolesFormType($this->rolesHelper);

        return array_merge($extensions, array(
            new PreloadedExtension(array($type), array()),
        ));
    }

    public function testSubmit()
    {
        $group = new TestGroup('foo');

        $form = $this->factory->create(LegacyFormHelper::getType('FOS\UserBundle\Form\Type\GroupFormType'), $group);
        $formData = array(
            'name' => 'bar',
            'roles' => array(),
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($group, $form->getData());
        $this->assertEquals('bar', $group->getName());
        $this->assertEquals(array(), $group->getRoles());
    }

    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new GroupFormType('FOS\UserBundle\Tests\TestGroup'),
        ));
    }
}

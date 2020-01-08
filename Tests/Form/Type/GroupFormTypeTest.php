<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\GroupFormType;
use FOS\UserBundle\Tests\TestGroup;

class GroupFormTypeTest extends TypeTestCase
{
    public function testSubmit()
    {
        $group = new TestGroup('foo');

        $form = $this->factory->create(GroupFormType::class, $group);
        $formData = [
            'name' => 'bar',
        ];
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($group, $form->getData());
        $this->assertSame('bar', $group->getName());
    }

    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), [
            new GroupFormType('FOS\UserBundle\Tests\TestGroup'),
        ]);
    }
}

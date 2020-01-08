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

use FOS\UserBundle\Form\Type\ResettingFormType;
use FOS\UserBundle\Tests\TestUser;

class ResettingFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();

        $form = $this->factory->create(ResettingFormType::class, $user);
        $formData = [
            'plainPassword' => [
                'first' => 'test',
                'second' => 'test',
            ],
        ];
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('test', $user->getPlainPassword());
    }

    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), [
            new ResettingFormType('FOS\UserBundle\Tests\TestUser'),
        ]);
    }
}

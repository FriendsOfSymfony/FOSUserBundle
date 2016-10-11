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
            'username' => 'bar',
            'email' => 'john@doe.com',
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('bar', $user->getUsername());
        $this->assertSame('john@doe.com', $user->getEmail());
    }

    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new ProfileFormType('FOS\UserBundle\Tests\TestUser'),
        ));
    }
}

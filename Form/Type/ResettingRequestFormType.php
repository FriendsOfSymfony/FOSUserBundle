<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResettingRequestFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            null,
            array(
                'translation_domain' => 'FOSUserBundle',
                'label' => 'resetting.request.username'
            )
        )
        ->add(
            'submit',
            'submit',
            array(
                'translation_domain' => 'FOSUserBundle',
                'label' => 'resetting.request.submit'
            )
        );
    }

    public function getName()
    {
        return 'fos_user_resetting_request';
    }
}

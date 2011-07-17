<?php

namespace FOS\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\DataTransformer\UsernameToUserTransformer;

/**
 * Takes a username as input,
 * exposes a User instance
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UsernameFormType extends AbstractType
{
    /**
     * @var UsernameToUserTransformer
     */
    protected $usernameTransformer;

    public function __construct(UsernameToUserTransformer $usernameTransformer)
    {
        $this->usernameTransformer = $usernameTransformer;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->appendClientTransformer($this->usernameTransformer);
    }

    public function getParent(array $options)
    {
        return 'text';
    }

    public function getName()
    {
        return 'fos_user_username';
    }
}

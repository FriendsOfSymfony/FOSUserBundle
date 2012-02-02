<?php

namespace FOS\UserBundle\Form\Type;

use FOS\UserBundle\Form\DataTransformer\UserToUsernameTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form type for representing a UserInterface instance by its username string.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UsernameFormType extends AbstractType
{
    /**
     * @var UserToUsernameTransformer
     */
    protected $usernameTransformer;

    /**
     * Constructor.
     *
     * @param UserToUsernameTransformer $usernameTransformer
     */
    public function __construct(UserToUsernameTransformer $usernameTransformer)
    {
        $this->usernameTransformer = $usernameTransformer;
    }

    /**
     * @see Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->prependClientTransformer($this->usernameTransformer);
    }

    /**
     * @see Symfony\Component\Form\AbstractType::getParent()
     */
    public function getParent(array $options)
    {
        return 'text';
    }

    /**
     * @see Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'fos_user_username';
    }
}

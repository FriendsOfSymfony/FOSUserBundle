<?php

namespace FOS\UserBundle\Form\Type;

use FOS\UserBundle\Form\DataTransformer\UserToEmailTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type for representing a UserInterface instance by its email string.
 *
 * @author Florian Krauthan <fkrauthan@gmx.net>
 */
class EmailFormType extends AbstractType
{
    /**
     * @var UserToEmailTransformer
     */
    protected $emailTransformer;

    /**
     * Constructor.
     *
     * @param UserToEmailTransformer $emailTransformer
     */
    public function __construct(UserToEmailTransformer $emailTransformer)
    {
        $this->emailTransformer = $emailTransformer;
    }

    /**
     * @see Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->emailTransformer);
    }

    /**
     * @see Symfony\Component\Form\AbstractType::getParent()
     */
    public function getParent()
    {
        return 'email';
    }

    /**
     * @see Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'fos_user_email';
    }
}

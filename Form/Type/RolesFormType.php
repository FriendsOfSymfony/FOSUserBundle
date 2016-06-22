<?php

namespace FOS\UserBundle\Form\Type;

use FOS\UserBundle\Util\LegacyFormHelper;
use FOS\UserBundle\Util\RolesHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type to select multiple roles from the Symfony security system.
 *
 * @author Jonny Schmid <jonny@fourlabs.co.uk>
 */
class RolesFormType extends AbstractType
{
    /**
     * @var RolesHelper
     */
    protected $rolesHelper;

    /**
     * Constructor.
     *
     * @param RolesHelper $rolesHelper
     */
    public function __construct(RolesHelper $rolesHelper)
    {
        $this->rolesHelper = $rolesHelper;
    }

    /**
     * @see Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'choices' => $this->rolesHelper->getRoles(),
            'choice_label' => function($role) {
                return $role;
            },
            'choice_translation_domain' => false,
        ]);
    }

    /**
     * @see Symfony\Component\Form\AbstractType::getParent()
     */
    public function getParent()
    {
        return LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\ChoiceType');
    }

    // BC for SF < 3.0
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'fos_user_roles';
    }
}

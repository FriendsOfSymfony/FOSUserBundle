<?php

namespace FOS\UserBundle\Form\Type;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\GroupManagerInterface;

use FOS\UserBundle\Form\ChoiceList\UserChoiceList;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UserChoiceType extends AbstractType
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var GroupManagerInterface
     */
    protected $groupManager;

    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * @var array
     */
    private $choiceListCache = array();

    /**
     * Constructor.
     *
     * @param UserManagerInterface      $userManager
     * @param GroupManagerInterface     $groupManager
     */
    public function __construct(UserManagerInterface $userManager, GroupManagerInterface $groupManager, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->userManager         = $userManager;
        $this->groupManager        = $groupManager;
        $this->propertyAccessor    = $propertyAccessor ?: PropertyAccess::getPropertyAccessor();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceListCache  =& $this->choiceListCache;
        $userManager      =  $this->userManager;
        $groupManager     =  $this->groupManager;
        $propertyAccessor = $this->propertyAccessor;

        $choiceList = function(Options $options) use (&$choiceListCache, $userManager, $groupManager, $propertyAccessor) {
            $choiceHashes = $options['choices'];

            // Support for recursive arrays
            if (is_array($choiceHashes)) {
                // A second parameter ($key) is passed, so we cannot use
                // spl_object_hash() directly (which strictly requires
                // one parameter)
                array_walk_recursive($choiceHashes, function (&$value) {
                    $value = spl_object_hash($value);
                });
            }

            $preferredChoiceHashes = $options['preferred_choices'];

            if (is_array($preferredChoiceHashes)) {
                array_walk_recursive($preferredChoiceHashes, function (&$value) {
                    $value = spl_object_hash($value);
                });
            }

            // Support for custom loaders (with query builders)
            $loaderHash = is_object($options['loader'])
                ? spl_object_hash($options['loader'])
                : $options['loader'];

            $hash = md5(json_encode(array(
                                          $loaderHash,
                                          $choiceHashes,
                                          $preferredChoiceHashes,
            )));
            
            if (!isset($choiceListCache[$hash])) {
                $choiceListCache[$hash] = new UserChoiceList(
                                                             $userManager,
                                                             $groupManager,
                                                             $options['loader'],
                                                             null,
                                                             $options['choices'],
                                                             $options['preferred_choices'],
                                                             null,
                                                             $propertyAccessor
                                                             );
            }

            return $choiceListCache[$hash];
        };

        $resolver->setDefaults(array(
                                     'choices'     => null,
                                     'choice_list' => $choiceList,
                                     'loader'      => null,
                                     'data_class'  => null,
                                     ));

        $resolver->setAllowedTypes(array(
                                         'loader' => array('null', 'Closure'),
                                         ));
    }

    /**
     * @see Symfony\Component\Form\AbstractType::getParent()
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @see Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'fos_user_choice';
    }
}

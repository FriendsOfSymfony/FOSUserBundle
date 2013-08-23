<?php
namespace FOS\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager (UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('groups')
            # FIXME looks ugly...
            #->add('roles', 'array')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true));
    }

    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username')
            ->add('locked')
            ->add('email')
            # FIXME would be cool :)
            #->add('roles')
            ->add('groups', null, array(), null, array('property' => 'name'));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('username')
                ->add('email')
            ->end()
            /* FIXME not implemented yet in SonataAdmin (it seems. At least without Group::__toString())
            ->with('Groups')
                ->add('groups', null, ['associated_tostring' => 'getName'])
            ->end()*/;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'text', array('required' => false, 'help' => 'Leave empty to keep it unchanged'))
                ->add('roles', 'collection', array('allow_add' => true, 'allow_delete' => true, 'options' => array('label' => false)))
            ->end()
            ->with('Groups')
                ->add('groups', 'sonata_type_model', array('required' => false, 'expanded' => true, 'multiple' => true, 'property' => 'name'))
            ->end();

        if (!$this->getSubject() || !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->with('Management')
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end();
        }
    }

    public function preUpdate($user)
    {
        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);

        return parent::preUpdate($user);
    }
}

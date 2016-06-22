The username Form Type
======================

FOSUserBundle provides a convenient username form type, named ``fos_user_username``.
It appears as a text input, accepts usernames and convert them to a User
instance::

    class MessageFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('recipient', 'FOS\UserBundle\Form\Type\UsernameFormType');

            // if you are using Symfony < 2.8 you should use the old name instead
            // $builder->add('recipient', 'fos_user_username');
        }
    }

.. note::

    If you don't use this form type in your app, you can disable it to remove
    the service from the container:

    .. code-block:: yaml

        # app/config/config.yml
        fos_user:
            use_username_form_type: false

The roles Form Type
======================

FOSUserBundle also provides a roles form type, named ``fos_user_roles``. It
appears as a select input with all roles from Symfony's security system as
options::

    class UserGroupFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('roles', 'FOS\UserBundle\Form\Type\RolesFormType');

            // if you are using Symfony < 2.8 you should use the old name instead
            // $builder->add('roles', 'fos_user_roles');
        }
    }

.. note::

    To find out more about roles, read the respective section in the
    `Security <http://symfony.com/doc/current/book/security.html#roles>`_
    chapter of the Symfony cookbook.

.. note::

    If you don't use this form type in your app, you can disable it to remove
    the service from the container:

    .. code-block:: yaml

        # app/config/config.yml
        fos_user:
            use_roles_form_type: false

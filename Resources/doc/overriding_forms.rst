Overriding Default FOSUserBundle Forms
======================================

Overriding a Form Type
----------------------

The default forms packaged with the FOSUserBundle provide functionality for
registering new user, updating your profile, changing your password and
much more. These forms work well with the bundle's default classes and controllers.
But, as you start to add more properties to your ``User``
class or you decide you want to add a few options to the registration form you
will find that you need to override the forms in the bundle.

Suppose that you have created an ORM user class with the following class name,
``AppBundle\Entity\User``. In this class, you have added a ``name`` property
because you would like to save the user's name as well as their username and
email address. Now, when a user registers for your site they should enter in their
name as well as their username, email and password. Below is an example ``$name``
property and its validators.

.. code-block:: php

    // src/AppBundle/Entity/User.php

    use FOS\UserBundle\Entity\User as BaseUser;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;

    class User extends BaseUser
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\Column(type="string", length=255)
         *
         * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
         * @Assert\Length(
         *     min=3,
         *     max=255,
         *     minMessage="The name is too short.",
         *     maxMessage="The name is too long.",
         *     groups={"Registration", "Profile"}
         * )
         */
        protected $name;

        // ...
    }

.. note::

    By default, the Registration validation group is used when validating a new
    user registration. Unless you have overridden this value in the configuration,
    make sure you add the validation group named Registration to your name property.

If you try and register using the default registration form you will find that
your new ``name`` property is not part of the form. You need to create a custom
form type and configure the bundle to use it.

The first step is to create a new form type in your own bundle. The following
class inherits from the base FOSUserBundle ``fos_user_registration`` type using
the form type hierarchy and then adds the custom ``name`` field.

.. code-block:: php

    <?php
    // src/AppBundle/Form/RegistrationType.php

    namespace AppBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class RegistrationType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('name');
        }

        public function getParent()
        {
            return 'fos_user_registration';
        }

        public function getName()
        {
            return 'app_user_registration';
        }
    }


.. note::

    If you don't want to reuse the fields added in FOSUserBundle by default,
    you can omit the ``getParent`` method and configure all fields yourself.

Now that you have created your custom form type, you must declare it as a service
and add a tag to it. The tag must have a ``name`` value of ``form.type`` and an ``alias``
value that is the equal to the string returned from the ``getName`` method of your
form type class. The ``alias`` that you specify is what you will use in the FOSUserBundle
configuration to let the bundle know that you want to use your custom form.

Below is an example of configuring your form type as a service:

.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.form.registration:
                class: AppBundle\Form\RegistrationType
                tags:
                    - { name: form.type, alias: app_user_registration }

    .. code-block:: xml

        <!-- app/config/services.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>

        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>

                <service id="app.form.registration" class="AppBundle\Form\RegistrationType">
                    <tag name="form.type" alias="app_user_registration" />
                </service>

            </services>

        </container>

Finally, you must update the configuration of the FOSUserBundle so that it will
use your form type instead of the default one. Below is the configuration for
changing the registration form type in YAML.

.. code-block:: yaml

    # app/config/config.yml
    fos_user:
        # ...
        registration:
            form:
                type: app_user_registration

Note how the ``alias`` value used in your form type's service configuration tag
is used in the bundle configuration to tell the FOSUserBundle to use your custom
form type.

Overriding Form Handlers
------------------------

There are two ways to override the default functionality provided by the
FOSUserBundle form handlers. The easiest way is to  override the ``onSuccess``
method of the handler. The ``onSuccess`` method is called after the form has been
bound and validated.

The second way is to override the ``process`` method. Overriding
the ``process`` method should only be necessary when more advanced functionality
is necessary when binding and validating the form.

Suppose you want to add some functionality that takes place after a successful
user registration. First you need to create a new class that extends
``FOS\UserBundle\Form\Handler\RegistrationFormHandler`` and then override the
protected ``onSuccess`` method.

.. code-block:: php

    <?php
    // src/AppBundle/Form/Handler/RegistrationFormHandler.php

    namespace AppBundle\Form\Handler;

    use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
    use FOS\UserBundle\Model\UserInterface;

    class RegistrationFormHandler extends BaseHandler
    {
        protected function onSuccess(UserInterface $user, $confirmation)
        {
            // Note: if you plan on modifying the user then do it before calling the
            // parent method as the parent method will flush the changes

            parent::onSuccess($user, $confirmation);

            // otherwise add your functionality here
        }
    }

.. note::

    If you do not call the onSuccess method of the parent class then the default
    logic that the FOSUserBundle handler normally executes upon a successful
    submission will not be performed.

You can also choose to override the ``process`` method of the handler. If you choose
to override the ``process`` method then you will be responsible for binding the form
data and validating it, as well as implementing the logic required upon a
successful submission.

.. code-block:: php

    <?php
    // src/AppBundle/Form/Handler/RegistrationFormHandler.php

    namespace AppBundle\Form\Handler;

    use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;

    class RegistrationFormHandler extends BaseHandler
    {
        public function process($confirmation = false)
        {
            $user = $this->userManager->createUser();
            $this->form->setData($user);

            if ('POST' == $this->request->getMethod()) {
                $this->form->bind($this->request);
                if ($this->form->isValid()) {

                    // do your custom logic here

                    return true;
                }
            }

            return false;
        }
    }

.. note::

    The process method should return true for a successful submission and false
    otherwise.

Now that you have created and implemented your custom form handler class, you
must configure it as a service in the container. Below is an example of
configuring your form handler as a service:

.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.form.handler.registration:
                class: AppBundle\Form\Handler\RegistrationFormHandler
                arguments: ["@fos_user.registration.form", "@request", "@fos_user.user_manager", "@fos_user.mailer", "@fos_user.util.token_generator"]
                scope: request
                public: false

    .. code-block:: xml

        <!-- app/config/services.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>

        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>

                <service id="app.form.handler.registration" class="AppBundle\Form\Handler\RegistrationFormHandler" scope="request" public="false">
                    <argument type="service" id="fos_user.registration.form" />
                    <argument type="service" id="request" />
                    <argument type="service" id="fos_user.user_manager" />
                    <argument type="service" id="fos_user.mailer" />
                    <argument type="service" id="fos_user.util.token_generator" />
                </service>

            </services>

        </container>

Here you have injected other services as arguments to the constructor of our class
because these arguments are required by the base FOSUserBundle form handler class
which you extended.

Now that your new form handler has been configured in the container, all that is
left to do is update the FOSUserBundle configuration.

.. code-block:: yaml

    # app/config/config.yml
    fos_user:
        # ...
        registration:
            form:
                handler: app.form.handler.registration

Note how the ``id`` of your configured service is used in the bundle configuration
to tell the FOSUserBundle to use your custom form handler.

At this point, when a user registers on your site your service will be used to
handle the form submission.

.. note::

    When you overwrite the form processing (be it only for the success logic
    or for the whole processing), don't forget to save the changes when the
    form is successful.
    This is done as part of the default success logic so you need to save it
    yourself if you don't call the original ``onSuccess`` method.

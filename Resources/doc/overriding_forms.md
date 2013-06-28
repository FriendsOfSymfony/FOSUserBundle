Overriding Default FOSUserBundle Forms
======================================

## Overriding a Form Type

The default forms packaged with the FOSUserBundle provide functionality for
registering new user, updating your profile, changing your password and
much more. These forms work well with the bundle's default classes and controllers.
But, as you start to add more properties to your `User`
class or you decide you want to add a few options to the registration form you
will find that you need to override the forms in the bundle.

Suppose that you have created an ORM user class with the following class name,
`Acme\UserBundle\Entity\User`. In this class, you have added a `name` property
because you would like to save the user's name as well as their username and
email address. Now, when a user registers for your site they should enter in their
name as well as their username, email and password. Below is an example `$name`
property and its validators.

``` php
// src/Acme/UserBundle/Entity/User.php
<?php

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
     *     max="255",
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $name;

    // ...
}
```

**Note:**

> By default, the Registration validation group is used when validating a new
> user registration. Unless you have overridden this value in the configuration,
> make sure you add the validation group named Registration to your name property.

If you try and register using the default registration form you will find that
your new `name` property is not part of the form. You need to create a custom
form type and configure the bundle to use it.

The first step is to create a new form type in your own bundle. The following
class extends the base FOSUserBundle `RegistrationFormType` and then adds the
custom `name` field.

``` php
// src/Acme/UserBundle/Form/Type/RegistrationFormType.php
<?php

namespace Acme\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('name');
    }

    public function getName()
    {
        return 'acme_user_registration';
    }
}
```

Now that you have created your custom form type, you must declare it as a service
and add a tag to it. The tag must have a `name` value of `form.type` and an `alias`
value that is the equal to the string returned from the `getName` method of your
form type class. The `alias` that you specify is what you will use in the FOSUserBundle
configuration to let the bundle know that you want to use your custom form.

Below is an example of configuring your form type as a service in XML:

``` xml
<!-- src/Acme/UserBundle/Resources/config/services.xml -->
<?xml version="1.0" encoding="UTF-8" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

        <service id="acme_user.registration.form.type" class="Acme\UserBundle\Form\Type\RegistrationFormType">
            <tag name="form.type" alias="acme_user_registration" />
            <argument>%fos_user.model.user.class%</argument>
        </service>

    </services>

</container>
```

Or if you prefer YAML:

``` yaml
# src/Acme/UserBundle/Resources/config/services.yml
services:
    acme_user.registration.form.type:
        class: Acme\UserBundle\Form\Type\RegistrationFormType
        arguments: [%fos_user.model.user.class%]
        tags:
            - { name: form.type, alias: acme_user_registration }
```

**Note:**

> In the form type service configuration you have specified the `fos_user.model.user.class`
> container parameter as a constructor argument. Unless you have redefined the
> constructor in your form type class, you must include this argument as it is a
> requirement of the FOSUserBundle form type that you extended.

Finally, you must update the configuration of the FOSUserBundle so that it will
use your form type instead of the default one. Below is the configuration for
changing the registration form type in YAML.

``` yaml
# app/config/config.yml
fos_user:
    # ...
    registration:
        form:
            type: acme_user_registration
```

Note how the `alias` value used in your form type's service configuration tag
is used in the bundle configuration to tell the FOSUserBundle to use your custom
form type.

**Note:**

> If you need to add some logic to the processing of the form, you can use
> a listener [hooking into the controller](controller_events.md).

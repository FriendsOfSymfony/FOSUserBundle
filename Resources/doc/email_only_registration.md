Email only registration
=======================

Email - Password authentication is a typical use case for web application.
Here is how you can configure FOSUserBundle to build a simple authentication form.

## FOSUserBundle installation
Follow the instructions as described in [FOSUserBundle documentation](index.md)

## Enable login by Username or Email
As described in [Logging by username or email](logging_by_username_or_email.md)
you must enable FOSUserBundle to use Email as security provider:

```yaml
# app/config/security.yml
security:
    providers:
        fos_userbundle:
            id: fos_user.user_provider.username_email
```

## Override login form
To build a minimalist authentication form (Email & Password), you must create a
specific Form (cfr [Overriding Forms](overriding_forms.md)).

### Create RegistrationFormType
```php
class RegistrationFormType extends BaseType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('username');
    }

    public function getName()
    {
        return 'acme_user_registration';
    }
}
```

### Define the form as a service
```xml
<service id="acme_user.registration.form.type" class="AcmeBundle\Form\Type\RegistrationFormType">
    <tag name="form.type" alias="acme_user_registration"/>
    <argument>%fos_user.model.user.class%</argument>
</service>
```

### Update fos_user configuration
```yaml
# app/config/config.yml
fos_user:
    # ...
    registration:
        form:
            type: acme_user_registration
            validation_groups: [AcmeRegistration]

```

If you are using Symfony 2.5, there is currently a bug [symfony/symfony#11227](https://github.com/symfony/symfony/issues/11227) with the validation framework.
You need to add the following configuration to downgrade validator to 2.4 version:
```yaml
framework:
    validation:
        enabled: true
        api: 2.4
        enable_annotations: true
```

### Add validation
Configure validation in 'AcmeBundle/Resources/config/validation.xml'
```xml
<?xml version="1.0" ?>
<constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping
        http://symfony.com/schema/dic/constraint-mapping/constraint-mapping-1.0.xsd">

    <class name="FOS\UserBundle\Model\User">
        <constraint name="Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity">
            <option name="fields">emailCanonical</option>
            <option name="errorPath">email</option>
            <option name="message">fos_user.email.already_used</option>
            <option name="groups">
                <value>AcmeRegistration</value>
            </option>
        </constraint>
    </class>

</constraint-mapping>
```

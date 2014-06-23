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
specific Form (cfr [Overriding Controllers](overriding_controllers.md)).

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
<service id="acme_user.registration.form.type" class="Data\UserBundle\Form\Type\RegistrationFormType">
    <tag name="form.type" alias="acme_user_registration"/>
    <argument>%fos_user.model.user.class%</argument>
</service>
```

### Update fos_user configuration
``` yaml
# app/config/config.yml
fos_user:
    # ...
    registration:
        form:
            type: acme_user_registration
```

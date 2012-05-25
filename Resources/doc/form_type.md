The username Form Type
======================

FOSUserBundle provides a convenient username form type, named ``fos_user_username``.
It appears as a text input, accepts usernames and convert them to a User
instance.

``` php
class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('recipient', 'fos_user_username');
    }
```

**Note:**

> If you don't use this form type in your app, you can disable it to remove
> the service from the container:

``` yaml
# app/config/config.yml
fos_user:
    use_username_form_type: false
```

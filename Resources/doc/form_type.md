FOSUserBundle Form Types
========================

The FOSUserBundle has two form types to convert username or email to a 
user instance.

### The username Form Type

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


### The email Form Type

FOSUserBundle provides a convenient email form type, named ``fos_user_email``.
It appears as a text input, accepts emails and convert them to a User
instance.

``` php
class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('recipient', 'fos_user_email');
    }
```

**Note:**

> If you don't use this form type in your app, you can disable it to remove
> the service from the container:

``` yaml
# app/config/config.yml
fos_user:
    use_email_form_type: false
```

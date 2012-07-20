FOSUserBundle Configuration Reference
=====================================

All available configuration options are listed below with their default values.

``` yaml
# app/config/config.yml
fos_user:
    db_driver:              ~ # Required
    firewall_name:          ~ # Required
    user_class:             ~ # Required
    use_listener:           true
    use_username_form_type: true
    model_manager_name:     null  # change it to the name of your entity/document manager if you don't want to use the default one.
    from_email:
        address:        webmaster@example.com
        sender_name:    Admin
    profile:
        form:
            type:               fos_user_profile
            handler:            fos_user.profile.form.handler.default
            name:               fos_user_profile_form
            validation_groups:  [Profile]
    change_password:
        form:
            type:               fos_user_change_password
            handler:            fos_user.change_password.form.handler.default
            name:               fos_user_change_password_form
            validation_groups:  [ChangePassword]
    registration:
        confirmation:
            from_email: # Use this node only if you don't want the global email address for the confirmation email
                address:        ...
                sender_name:    ...
            enabled:    false # change to true for required email confirmation
            template:   FOSUserBundle:Registration:email.txt.twig
        form:
            type:               fos_user_registration
            handler:            fos_user.registration.form.handler.default
            name:               fos_user_registration_form
            validation_groups:  [Registration]
    resetting:
        token_ttl: 86400
        email:
            from_email: # Use this node only if you don't want the global email address for the resetting email
                address:        ...
                sender_name:    ...
            template:   FOSUserBundle:Resetting:email.txt.twig
        form:
            type:               fos_user_resetting
            handler:            fos_user.resetting.form.handler.default
            name:               fos_user_resetting_form
            validation_groups:  [ResetPassword]
    service:
        mailer:                 fos_user.mailer.default
        email_canonicalizer:    fos_user.util.canonicalizer.default
        username_canonicalizer: fos_user.util.canonicalizer.default
        token_generator:        fos_user.util.token_generator.default
        user_manager:           fos_user.user_manager.default
    template:
        engine: twig
    group:
        group_class:    ~ # Required when using groups
        group_manager:  fos_user.group_manager.default
        form:
            type:               fos_user_group
            handler:            fos_user.group.form.handler.default
            name:               fos_user_group_form
            validation_groups:  [Registration]
```

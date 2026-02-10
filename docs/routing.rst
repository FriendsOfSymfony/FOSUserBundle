Advanced routing configuration
==============================

By default, the routing file ``@FOSUserBundle/Resources/config/routing/all.php`` imports
all the routing files and enables all the routes.
In the case you want to enable or disable the different available routes, just use the
single routing configuration files.

.. configuration-block::

    .. code-block:: yaml

        # app/config/routing.yml
        fos_user_security:
            resource: "@FOSUserBundle/Resources/config/routing/security.php"

        fos_user_profile:
            resource: "@FOSUserBundle/Resources/config/routing/profile.php"
            prefix: /profile

        fos_user_register:
            resource: "@FOSUserBundle/Resources/config/routing/registration.php"
            prefix: /register

        fos_user_resetting:
            resource: "@FOSUserBundle/Resources/config/routing/resetting.php"
            prefix: /resetting

        fos_user_change_password:
            resource: "@FOSUserBundle/Resources/config/routing/change_password.php"
            prefix: /profile

    .. code-block:: xml

        <!-- app/config/routing.xml -->
        <import resource="@FOSUserBundle/Resources/config/routing/security.php"/>
        <import resource="@FOSUserBundle/Resources/config/routing/profile.php" prefix="/profile" />
        <import resource="@FOSUserBundle/Resources/config/routing/registration.php" prefix="/register" />
        <import resource="@FOSUserBundle/Resources/config/routing/resetting.php" prefix="/resetting" />
        <import resource="@FOSUserBundle/Resources/config/routing/change_password.php" prefix="/profile" />

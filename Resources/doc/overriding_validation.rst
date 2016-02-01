Overriding Default FOSUserBundle Validation
===========================================

The files located in ``Resources/config/storage-validation`` directory contains definitions for custom
validator rules for various storage engines. The rules defined by FOSUserBundle are
all in validation groups so you can choose not to use them.

You can overwrite the default settings by creating a new validation file in your bundle. This is bundle inheritance. Just copy the respective storage validation resource (couchdb.xml, mongodb.xml, propel.xml respectively) from ``Resources/config/storage-validation`` to ``YourBundle/Resources/config/storage-validation`` directory and adjust it to your needs. **Change the class name**, then add your constraints.

Read more about which constraints are available (and how to use them with xml configuration) in the Validation Constraints Reference http://symfony.com/doc/current/reference/constraints.html


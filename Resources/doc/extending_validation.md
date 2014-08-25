Overriding Default FOSUserBundle Validation
===========================================

The `Resources/config/validation.xml` file contains definitions for custom
validator rules for various classes. The rules defined by FOSUserBundle are
all in validation groups so you can choose not to use them.

The simplest way to change the rules is to copy the file into the Resources/config 
directory of the bundle where you defined your user class.

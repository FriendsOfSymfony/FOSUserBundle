FOSUserBundle Canonicalization
==============================

FOSUserBundle stores canonicalized versions of the username and the email
which are used when querying and checking for uniqueness.
The default implementation simply makes them case-insensitive to avoid having
users whose username only differs because of the case. It uses  ``mb_convert_case()``
to achieve this result.

.. caution::

    If you do not have the mbstring extension installed you will need to define
    your own ``canonicalizer``.

Replacing the Canonicalizers
----------------------------

If you want to change the way the caconical fields are populated, simply
create a class implementing ``FOS\UserBundle\Util\CanonicalizerInterface``
and register it as a service:

.. code-block:: yaml

    # app/config/config.yml
    services:
        my_canonicalizer:
            class: Acme\UserBundle\Util\CustomCanonicalizer
            public: false

You can now configure FOSUserBundle to use your own implementation:

.. code-block:: yaml

    # app/config/config.yml
    fos_user:
        # ...
        service:
            email_canonicalizer:    my_canonicalizer
            username_canonicalizer: my_canonicalizer

You can of course use different services for each field if you don't want
to use the same logic.

.. note::

    The default implementation has the id ``fos_user.util.canonicalizer.default``.

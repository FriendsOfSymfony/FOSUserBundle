Overriding Default FOSUserBundle Templates
==========================================

As you start to incorporate FOSUserBundle into your application, you will probably
find that you need to override the default templates that are provided by
the bundle. Although the template names are not configurable, Symfony
provides a built-in way to `override the templates themselves`_.

Example: Overriding The Default layout.html.twig
------------------------------------------------

It is highly recommended that you override the ``Resources/views/layout.html.twig``
template so that the pages provided by the FOSUserBundle have a similar look and
feel to the rest of your application. An example of overriding this layout template
is demonstrated below using both of the overriding options listed above.

Here is the default ``layout.html.twig`` provided by the FOSUserBundle:

.. code-block:: html+jinja

    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8" />
        </head>
        <body>
            <div>
                {% if is_granted("IS_AUTHENTICATED_REMEMBERED") %}
                    {{ 'layout.logged_in_as'|trans({'%username%': app.user.username}, 'FOSUserBundle') }} |
                    <a href="{{ path('fos_user_security_logout') }}">
                        {{ 'layout.logout'|trans({}, 'FOSUserBundle') }}
                    </a>
                {% else %}
                    <a href="{{ path('fos_user_security_login') }}">{{ 'layout.login'|trans({}, 'FOSUserBundle') }}</a>
                {% endif %}
            </div>

            {% if app.request.hasPreviousSession %}
                {% for type, messages in app.session.flashBag.all %}
                    {% for message in messages %}
                        <div class="{{ type }}">
                            {{ message|trans({}, 'FOSUserBundle') }}
                        </div>
                    {% endfor %}
                {% endfor %}
            {% endif %}

            <div>
                {% block fos_user_content %}
                {% endblock fos_user_content %}
            </div>
        </body>
    </html>

As you can see its pretty basic and doesn't really have much structure, so you will
want to replace it with a layout file that is appropriate for your application. The
main thing to note in this template is the block named ``fos_user_content``. This is
the block where the content from each of the different bundle's actions will be
displayed, so you must make sure to include this block in the layout file you will
use to override the default one.

The following Twig template file is an example of a layout file that might be used
to override the one provided by the bundle.

.. code-block:: html+jinja

    {% block title %}Demo Application{% endblock %}

    {% block content %}
        {% block fos_user_content %}{% endblock %}
    {% endblock %}

This example extends the layout template from the layout of your app. The
``content`` block is where the main content of each page is rendered. This
is why the ``fos_user_content`` block has been placed inside of it. This
will lead to the desired effect of having the output from the FOSUserBundle
actions integrated into our applications layout, preserving the look and
feel of the application.

The easiest way to override a bundle's template is to simply place a new one in
your ``app/Resources`` folder. To override the layout template located at
``Resources/views/layout.html.twig`` in the ``FOSUserBundle`` directory, you would place
your new layout template at ``app/Resources/FOSUserBundle/views/layout.html.twig``.

As you can see the pattern for overriding templates in this way is to
create a folder with the name of the bundle class in the ``app/Resources`` directory.
Then add your new template to this folder, preserving the directory structure from the
original bundle.

After overriding a template, you must clear the cache for the override to
take effect, even in a development environment.

Overriding all of the other templates provided by the FOSUserBundle can be done
in a similar fashion using either of the two methods shown in this document.

.. _`override the templates themselves`: https://symfony.com/doc/current/templating/overriding.html

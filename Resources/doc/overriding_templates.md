Overriding Default FOSUserBundle Templates
==========================================

As you start to incorporate FOSUserBundle into your application, you will probably
find that you need to override the default templates that are provided by
the bundle. Although the template names are not configurable, the Symfony2
framework provides two ways to override the templates of a bundle.

1. Define a new template of the same name in the `app/Resources` directory
2. Create a new bundle that is defined as a child of `FOSUserBundle`

### Example: Overriding The Default layout.html.twig

It is highly recommended that you override the `Resources/views/layout.html.twig`
template so that the pages provided by the FOSUserBundle have a similar look and
feel to the rest of your application. An example of overriding this layout template
is demonstrated below using both of the overriding options listed above.

Here is the default `layout.html.twig` provided by the FOSUserBundle:

``` html+jinja
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

        {% for type, messages in app.session.flashBag.all %}
            {% for message in messages %}
                <div class="{{ type }}">
                    {{ message|trans({}, 'FOSUserBundle') }}
                </div>
            {% endfor %}
        {% endfor %}

        <div>
            {% block fos_user_content %}
            {% endblock fos_user_content %}
        </div>
    </body>
</html>
```

As you can see its pretty basic and doesn't really have much structure, so you will
want to replace it with a layout file that is appropriate for your application. The
main thing to note in this template is the block named `fos_user_content`. This is
the block where the content from each of the different bundle's actions will be
displayed, so you must make sure to include this block in the layout file you will
use to override the default one.

The following Twig template file is an example of a layout file that might be used
to override the one provided by the bundle.

``` html+jinja
{% extends 'AcmeDemoBundle::layout.html.twig' %}

{% block title %}Acme Demo Application{% endblock %}

{% block content %}
    {% block fos_user_content %}{% endblock %}
{% endblock %}
```

This example extends the layout template from a fictional application bundle named
`AcmeDemoBundle`. The `content` block is where the main content of each page is rendered.
This is why the `fos_user_content` block has been placed inside of it. This will
lead to the desired effect of having the output from the FOSUserBundle actions
integrated into our applications layout, preserving the look and feel of the
application.

**a) Define New Template In app/Resources**

The easiest way to override a bundle's template is to simply place a new one in
your `app/Resources` folder. To override the layout template located at
`Resources/views/layout.html.twig` in the `FOSUserBundle` directory, you would place
your new layout template at `app/Resources/FOSUserBundle/views/layout.html.twig`.

As you can see the pattern for overriding templates in this way is to
create a folder with the name of the bundle class in the `app/Resources` directory.
Then add your new template to this folder, preserving the directory structure from the
original bundle.

**b) Create A Child Bundle And Override Template**

**Note:**

```
This method is more complicated than the one outlined above. Unless  you are
planning to override the controllers as well as the templates, it is recommended
that you use the other method.
```

As listed above, you can also create a bundle defined as child of FOSUserBundle
and place the new template in the same location that is resides in the FOSUserBundle.
The first thing you want to do is override the `getParent` method to your bundle
class.

``` php
// src/Acme/UserBundle/AcmeUserBundle.php
<?php

namespace Acme\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
```

By returning the name of the bundle in the `getParent` method of your bundle class,
you are telling the Symfony2 framework that your bundle is a child of the FOSUserBundle.

Now that you have declared your bundle as a child of the FOSUserBundle, you can override
the parent bundle's templates. To override the layout template, simply create a new file
in the `src/Acme/UserBundle/Resources/views` directory named `layout.html.twig`. Notice
how this file resides in the same exact path relative to the bundle directory as it
does in the FOSUserBundle.

After overriding a template in your child bundle, you must clear the cache for the override
to take effect, even in a development environment.

Overriding all of the other templates provided by the FOSUserBundle can be done
in a similar fashion using either of the two methods shown in this document.

### Configuring A Templating Engine Other Than Twig

You can configure a templating engine other than Twig using the bundle's configuration.
Below is an example configuration for using the PHP templating engine.

``` yaml
fos_user:
    # ...
    template:
        engine: php
```

The FOSUserBundle only provides default templates for the Twig templating engine,
so you will have to create all of the templates that you are using. The names and
locations will be the same except that the file extension will be `.php` instead of
`.twig`

Overriding Default FOSUserBundle Controllers
============================================

The default controllers packaged with the FOSUserBundle provide a lot of
functionality that is sufficient for general use cases. But, you might find
that you need to extend that functionality and add some logic that suits the
specific needs of your application.

The first step to overriding a controller in the bundle is to create a child
bundle whose parent is FOSUserBundle. The following code snippet creates a new
bundle named `AcmeUserBundle` that declares itself a child of FOSUserBundle.

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

**Note:**

> The Symfony2 framework only allows a bundle to have one child. You cannot create
> another bundle that is also a child of FOSUserBundle.


Now that you have created the new child bundle you can simply create a controller class
with the same name and in the same location as the one you want to override. This
example overrides the `RegistrationController` by extending the FOSUserBundle
`RegistrationController` class and simply overriding the method that needs the extra
functionality.

The example below overrides the `registerAction` method. It uses the code from
the base controller and adds logging a new user registration to it.

``` php
// src/Acme/UserBundle/Controller/RegistrationController.php
<?php

namespace Acme\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            /*****************************************************
             * Add new functionality (e.g. log the registration) *
             *****************************************************/
            $this->container->get('logger')->info(
                sprintf('New user registration: %s', $user)
            );

            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $this->authenticateUser($user);
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);

            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
    }
}
```

**Note:**

> If you do not extend the FOSUserBundle controller class that you want to override
> and instead extend ContainerAware or the Controller class provided by the FrameworkBundle
> then you must implement all of the methods of the FOSUserBundle controller that
> you are overriding.

Overriding Default FOSUserBundle Controllers
============================================

The default controllers packaged with the FOSUserBundle provide a lot of
functionality that is sufficient for general use cases. But, you might find
that you need to extend that functionality and add some logic that suits the
specific needs of your application.

**Note:**

> Overriding the controller requires to duplicate all the logic of the action.
> Most of the time, it is easier to use the [events](controller_events.md)
> to implement the functionality. Replacing the whole controller should be
> considered as the last solution when nothing else is possible.

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

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                /*****************************************************
                 * Add new functionality (e.g. log the registration) *
                 *****************************************************/
                $this->container->get('logger')->info(
                    sprintf('New user registration: %s', $user)
                );
                
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
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

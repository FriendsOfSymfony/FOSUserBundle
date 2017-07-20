Overriding Default FOSUserBundle Controllers
============================================

The default controllers packaged with the FOSUserBundle provide a lot of
functionality that is sufficient for general use cases. But, you might find
that you need to extend that functionality and add some logic that suits the
specific needs of your application.

.. caution::

    Overriding the controller requires to duplicate all the logic of the
    action. Most of the time, it is easier to use the :doc:`events </controller_events>`
    to implement the functionality. Replacing the whole controller should
    be considered as the last solution when nothing else is possible.

The first step to overriding a controller in the bundle is to configure
FOSUserBundle to be a parent of your ``AppBundle``.

.. code-block:: php

    <?php
    // src/AppBundle/AppBundle.php

    namespace AppBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AppBundle extends Bundle
    {
        public function getParent()
        {
            return 'FOSUserBundle';
        }
    }

.. note::

    The Symfony Framework only allows a bundle to have one child. You cannot
    create another bundle that is also a child of FOSUserBundle.

.. note::

    You could also create a new child bundle and declare it as a child of FOSUserBundle.

Now that your bundle is correctly configured you can simply create a controller class
with the same name and in the same location as the one you want to override. This
example overrides the ``RegistrationController`` by extending the FOSUserBundle
``RegistrationController`` class and simply overriding the method that needs the extra
functionality.

The example below overrides the ``registerAction`` method. It uses the code from
the base controller and adds logging a new user registration to it.

.. code-block:: php

    <?php
    // src/AppBundle/Controller/RegistrationController.php

    namespace AppBundle\Controller;

    use Symfony\Component\HttpFoundation\RedirectResponse;
    use FOS\UserBundle\Controller\RegistrationController as BaseController;
    use Symfony\Component\HttpFoundation\Request;

    class RegistrationController extends BaseController
    {
        public function registerAction(Request $request)
        {
            /** @var $formFactory FactoryInterface */
            $formFactory = $this->get('fos_user.registration.form.factory');
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            /** @var $dispatcher EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            $user = $userManager->createUser();
            $user->setEnabled(true);

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $form = $formFactory->createForm();
            $form->setData($user);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $event = new FormEvent($form, $request);
                    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                    $userManager->updateUser($user);

                    /*****************************************************
                     * Add new functionality (e.g. log the registration) *
                     *****************************************************/
                    $this->container->get('logger')->info(
                        sprintf("New user registration: %s", $user)
                    );

                    if (null === $response = $event->getResponse()) {
                        $url = $this->generateUrl('fos_user_registration_confirmed');
                        $response = new RedirectResponse($url);
                    }

                    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                    return $response;
                }

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

                if (null !== $response = $event->getResponse()) {
                    return $response;
                }
            }

            return $this->render('@FOSUser/Registration/register.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

.. note::

    If you do not extend the FOSUserBundle controller class that you want
    to override and instead extend ContainerAware or the Controller class
    provided by the FrameworkBundle then you must implement all of the methods
    of the FOSUserBundle controller that you are overriding.

Send data to templates
----------------------

If you need to send specific data to the templates but don't want to override
the main method, just proceed as above to override the Controller and then,
override the method `setUserData()` by providing an array which will be available as
`data` in the templates.

.. code-block:: php

    <?php
    // src/AppBundle/Controller/RegistrationController.php

    namespace AppBundle\Controller;

    use Symfony\Component\HttpFoundation\RedirectResponse;
    use FOS\UserBundle\Controller\RegistrationController as BaseController;
    use Symfony\Component\HttpFoundation\Request;

    class RegistrationController extends BaseController
    {
        public function setUserData()
        {
            return array(
                //Accessible as data.value in Templates rendered by RegistrationController
                'value' => 'my value',
            );
        }
    }

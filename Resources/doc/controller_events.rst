Hooking into the controllers
============================

The controllers packaged with the FOSUserBundle provide a lot of
functionality that is sufficient for general use cases. But, you might find
that you need to extend that functionality and add some logic that suits the
specific needs of your application.

For this purpose, the controllers are dispatching events in many places in
their logic. All events can be found in the constants of the
``FOS\UserBundle\FOSUserEvents`` class.

All controllers follow the same convention: they dispatch a ``SUCCESS`` event
when the form is valid before saving the user, and a ``COMPLETED`` event when
it is done. Thus, all ``SUCCESS`` events allow you to set a response if you
don't want the default redirection. and all ``COMPLETED`` events give you access
to the response before it is returned.

Controllers with a form also dispatch an ``INITIALIZE`` event after the entity is
fetched, but before the form is created.

For instance, this listener will change the redirection after the password
resetting to go to the homepage instead of the profile::

    // src/Acme/UserBundle/EventListener/PasswordResettingListener.php

    namespace Acme\UserBundle\EventListener;

    use FOS\UserBundle\FOSUserEvents;
    use FOS\UserBundle\Event\FormEvent;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    /**
     * Listener responsible to change the redirection at the end of the password resetting
     */
    class PasswordResettingListener implements EventSubscriberInterface
    {
        private $router;

        public function __construct(UrlGeneratorInterface $router)
        {
            $this->router = $router;
        }

        /**
         * {@inheritdoc}
         */
        public static function getSubscribedEvents()
        {
            return array(
                FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingSuccess',
            );
        }

        public function onPasswordResettingSuccess(FormEvent $event)
        {
            $url = $this->router->generate('homepage');

            $event->setResponse(new RedirectResponse($url));
        }
    }

You can then register this listener:

.. configuration-block::

    .. code-block:: yaml

        # src/Acme/UserBundle/Resources/config/services.yml
        services:
            acme_user.password_resetting:
                class: Acme\UserBundle\EventListener\PasswordResettingListener
                arguments: ['@router']
                tags:
                    - { name: kernel.event_subscriber }

    .. code-block:: xml

        <!-- src/Acme/UserBundle/Resources/config/services.xml -->
        <service id="acme_user.password_resetting" class="Acme\UserBundle\EventListener\PasswordResettingListener">
            <tag name="kernel.event_subscriber"/>
            <argument type="service" id="router"/>
        </service>

Registration success listener with enabled confirmation at the same time
------------------------------------------------------------------------

When you have registration confirmation and you want to hook up to
``FOSUserEvents::REGISTRATION_SUCCESS`` event you will have to prioritize this listener to be called
before ``FOS\UserBundle\EventListener\EmailConfirmationListener::onRegistrationSuccess``::

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => [
                ['onRegistrationSuccess', -10],
            ],
        ];
    }

If you don't do it, ``EmailConfirmationListener`` will be called earlier and you will be redirected to
``fos_user_registration_check_email`` route.
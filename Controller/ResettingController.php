<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Event\ViewEvent;

/**
 * Controller managing the resetting of the password
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends ContainerAware
{
    const SESSION_EMAIL = 'fos_user_send_resetting_email/email';

    /**
     * Request reset user password: show form
     */
    public function requestAction(Request $request)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new ViewEvent(array(), $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_REQUEST_VIEW, $event);

        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.' . $this->getEngine());
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            $context = array('invalid_username' => $username);
            $event = new ViewEvent($context, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_REQUEST_VIEW, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.' . $this->getEngine(), $context);
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            $event = new ViewEvent(array('user' => $user), $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_ALREADY_REQUESTED_VIEW, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.' . $this->getEngine());
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        $event = new ViewEvent(array('user' => $user), $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_REQUEST_COMPLETED_VIEW, $event);

        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $session = $this->container->get('session');
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:checkEmail.html.'.$this->getEngine(), array(
            'email' => $email,
        ));
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        $context = array(
            'token' => $token,
            'form' => $form->createView()
        );

        $event = new ViewEvent($context, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_VIEW, $event);

        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:reset.html.' . $this->getEngine(), $context);
    }

    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }
}

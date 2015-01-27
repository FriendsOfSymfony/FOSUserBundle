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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
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

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render('FOSUserBundle:Registration:checkEmail.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('FOSUserBundle:Registration:confirmed.html.twig', array(
            'user' => $user,
        ));
    }
}

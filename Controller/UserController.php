<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Controller;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Form\ChangePassword;
use FOS\UserBundle\Form\ResetPassword;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Exception\AccessDeniedException;

/**
 * RESTful controller managing user CRUD
 */
class UserController extends Controller
{
    /**
     * Show all users
     */
    public function listAction()
    {
        $users = $this->get('fos_user.user_manager')->findUsers();

        return $this->render('FOSUserBundle:User:list.html.'.$this->getEngine(), array('users' => $users));
    }

    /**
     * Show one user
     */
    public function showAction($username)
    {
        $user = $this->findUserBy('username', $username);
        return $this->render('FOSUserBundle:User:show.html.'.$this->getEngine(), array('user' => $user));
    }

    /**
     * Edit one user, show the edit form
     */
    public function editAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $form = $this->createForm($user);

        return $this->render('FOSUserBundle:User:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'username'  => $user->getUsername()
        ));
    }

    /**
     * Update a user
     */
    public function updateAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $form = $this->createForm($user);
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user);
            $this->setFlash('fos_user_user_update', 'success');
            $userUrl = $this->generateUrl('fos_user_user_show', array('username' => $user->getUsername()));
            return $this->redirect($userUrl);
        }

        return $this->render('FOSUserBundle:User:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'username'  => $user->getUsername()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->createForm();

        return $this->render('FOSUserBundle:User:new.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Create a user and send a confirmation email
     */
    public function createAction()
    {
        $form = $this->createForm();
        // TODO: fix once the FormFactory is updated
        // $form->setValidationGroups('Registration');
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $user = $form->getData();
            if ($this->container->getParameter('fos_user.email.confirmation.enabled')) {
                $user->setEnabled(false);
                $this->get('fos_user.user_manager')->updateUser($user);
                $this->sendConfirmationEmailMessage($user);
                $this->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $url = $this->generateUrl('fos_user_user_check_confirmation_email');
            } else {
                $user->setConfirmationToken(null);
                $user->setEnabled(true);
                $this->get('fos_user.user_manager')->updateUser($user);
                $this->authenticateUser($user);
                $url = $this->generateUrl('fos_user_user_confirmed');
            }

            if ($this->container->has('security.acl.provider')) {
                $provider = $this->container->get('security.acl.provider');
                $acl = $provider->createAcl(ObjectIdentity::fromDomainObject($user));
                $acl->insertObjectAce(UserSecurityIdentity::fromAccount($user), MaskBuilder::MASK_OWNER);
                $provider->updateAcl($acl);
            }

            $this->setFlash('fos_user_user_create', 'success');
            return $this->redirect($url);
        }

        return $this->render('FOSUserBundle:User:new.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkConfirmationEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->findUserBy('email', $email);

        $this->setFlash('fos_user_user_confirm', 'success');

        return $this->render('FOSUserBundle:User:checkConfirmationEmail.html.'.$this->getEngine(), array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction($token)
    {
        $user = $this->findUserBy('confirmationToken', $token);
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $this->get('fos_user.user_manager')->updateUser($user);
        $this->authenticateUser($user);

        return $this->redirect($this->generateUrl('fos_user_user_confirmed'));
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        $this->setFlash('fos_user_user_confirmed', 'success');
        return $this->render('FOSUserBundle:User:confirmed.html.'.$this->getEngine(), array(
            'user' => $user,
        ));
    }

    /**
     * Delete one user
     */
    public function deleteAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $this->get('fos_user.user_manager')->deleteUser($user);
        $this->setFlash('fos_user_user_delete', 'success');

        return $this->redirect($this->generateUrl('fos_user_user_list'));
    }

    /**
     * Change user password: show form
     */
    public function changePasswordAction()
    {
        $user = $this->getUser();
        $form = $this->createChangePasswordForm($user);

        return $this->render('FOSUserBundle:User:changePassword.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Change user password: submit form
     */
    public function changePasswordUpdateAction()
    {
        $user = $this->getUser();
        $form = $this->createChangePasswordForm($user);
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $user->setPlainPassword($form->getNewPassword());
            $this->get('fos_user.user_manager')->updateUser($user);
            $this->setFlash('fos_user_user_password', 'success');

            $userUrl = $this->generateUrl('fos_user_user_show', array('username' => $user->getUsername()));

            return $this->redirect($userUrl);
        }

        return $this->render('FOSUserBundle:User:changePassword.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Request reset user password: show form
     */
    public function requestResetPasswordAction()
    {
        return $this->render('FOSUserBundle:User:requestResetPassword.html.'.$this->getEngine());
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendResettingEmailAction()
    {
        $user = $this->findUserBy('username', $this->get('request')->request->get('username'));

        if ((null !== $lastRequested = $user->getPasswordRequestedAt()) && $lastRequested->getTimestamp() + 86400 > time()) {
            return $this->render('FOSUserBundle:User:passwordAlreadyRequested.html.'.$this->getEngine());
        }

        $user->generateConfirmationToken();
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);
        $this->get('session')->set('fos_user_send_resetting_email/email', $user->getEmail());
        $this->sendResettingEmailMessage($user);

        return $this->redirect($this->generateUrl('fos_user_user_check_resetting_email'));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkResettingEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_resetting_email/email');
        $this->get('session')->remove('fos_user_send_resetting_email/email');
        $user = $this->findUserBy('email', $email);
        $this->setFlash('fos_user_user_reset', 'success');

        return $this->render('FOSUserBundle:User:checkResettingEmail.html.'.$this->getEngine(), array(
            'user' => $user,
        ));
    }

    /**
     * Reset user password: show form
     */
    public function resetPasswordAction($token)
    {
        $user = $this->findUserBy('confirmationToken', $token);

        if ((null === $lastRequested = $user->getPasswordRequestedAt()) || $lastRequested->getTimestamp() + 86400 < time()) {
            $this->redirect($this->generateUrl('fos_user_user_request_reset_password'));
        }

        $form = $this->createResetPasswordForm($user);

        return $this->render('FOSUserBundle:User:resetPassword.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form
        ));
    }

    /**
     * Reset user password: submit form
     */
    public function resetPasswordUpdateAction($token)
    {
        $user = $this->findUserBy('confirmationToken', $token);

        if ((null === $lastRequested = $user->getPasswordRequestedAt()) || $lastRequested->getTimestamp() + 86400 < time()) {
            $this->redirect($this->generateUrl('fos_user_user_request_reset_password'));
        }

        $form = $this->createResetPasswordForm($user);
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $user->setPlainPassword($form->getNewPassword());
            $user->setConfirmationToken(null);
            $user->setEnabled(true);
            $this->get('fos_user.user_manager')->updateUser($user);
            $this->authenticateUser($user);
            $userUrl = $this->generateUrl('fos_user_user_show', array('username' => $user->getUsername()));
            $this->setFlash('fos_user_user_resetted', 'success');

            return $this->redirect($userUrl);
        }

        return $this->render('FOSUserBundle:User:resetPassword.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form
        ));
    }

    /**
     * Get a user from the security context
     *
     * @throws AccessDeniedException if no user is authenticated
     * @return User
     */
    protected function getUser()
    {
        $user = $this->get('security.context')->getUser();
        if (!$user) {
            throw new AccessDeniedException('A logged in user is required.');
        }

        return $user;
    }

    /**
     * Find a user by a specific property
     *
     * @param string $key property name
     * @param mixed $value property value
     * @throws NotFoundException if user does not exist
     * @return User
     */
    protected function findUserBy($key, $value)
    {
        if (!empty($value)) {
            $user = $this->get('fos_user.user_manager')->{'findUserBy'.ucfirst($key)}($value);
        }

        if (empty($user)) {
            throw new NotFoundHttpException(sprintf('The user with "%s" does not exist for value "%s"', $key, $value));
        }

        return $user;
    }

    /**
     * Authenticate a user with Symfony Security
     *
     * @param Boolean $reAuthenticate
     * @return null
     */
    protected function authenticateUser(User $user, $reAuthenticate = false)
    {
        $token = new UsernamePasswordToken($user, null, $user->getRoles());

        if (true === $reAuthenticate) {
            $token->setAuthenticated(false);
        }

        $this->get('security.context')->setToken($token);
    }

    /**
     * Create a UserForm instance and returns it
     *
     * @param User $object
     * @return FOS\UserBundle\Form\UserForm
     */
    protected function createForm($object = null)
    {
        $form = $this->get('fos_user.form.user');
        if (null === $object) {
            $object = $this->get('fos_user.user_manager')->createUser();
        }

        $form->setData($object);

        return $form;
    }

    protected function createChangePasswordForm(User $user)
    {
        $form = $this->get('fos_user.form.change_password');
        $form->setData(new ChangePassword($user));

        return $form;
    }

    protected function createResetPasswordForm(User $user)
    {
        $form = $this->get('fos_user.form.reset_password');
        $form->setData(new ResetPassword($user));

        return $form;
    }

    protected function sendConfirmationEmailMessage(User $user)
    {
        $template = $this->container->getParameter('fos_user.email.confirmation.template');
        $rendered = $this->renderView($template.'.txt.'.$this->getEngine(), array(
            'user' => $user,
            'confirmationUrl' => $this->generateUrl('fos_user_user_confirm', array('token' => $user->getConfirmationToken()), true)
        ));
        $this->sendEmailMessage($rendered, $this->getSenderEmail('confirmation'), $user->getEmail());
    }

    protected function sendResettingEmailMessage(User $user)
    {
        $template = $this->container->getParameter('fos_user.email.resetting_password.template');
        $rendered = $this->renderView($template.'.txt.'.$this->getEngine(), array(
            'user' => $user,
            'confirmationUrl' => $this->generateUrl('fos_user_user_reset_password', array('token' => $user->getConfirmationToken()), true)
        ));
        $this->sendEmailMessage($rendered, $this->getSenderEmail('resetting_password'), $user->getEmail());
    }

    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $mailer = $this->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $mailer->send($message);
    }

    protected function setFlash($action, $value)
    {
        $this->get('session')->setFlash($action, $value);
    }

    protected function getSenderEmail($type)
    {
        return $this->container->getParameter('fos_user.email.from_email');
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.renderer');
    }
}

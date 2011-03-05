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
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Model\UserInterface;

/**
 * RESTful controller managing user CRUD
 */
class UserController extends ContainerAware
{
    /**
     * Show all users
     */
    public function listAction()
    {
        $users = $this->container->get('fos_user.user_manager')->findUsers();

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:list.html.'.$this->getEngine(), array('users' => $users));
    }

    /**
     * Show one user
     */
    public function showAction($username)
    {
        $user = $this->findUserBy('username', $username);
        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:show.html.'.$this->getEngine(), array('user' => $user));
    }

    /**
     * Edit one user, show the edit form
     */
    public function editAction($username)
    {
        $user = $this->findUserBy('username', $username);
        
        $this->checkPermission($user);

        $form = $this->container->get('fos_user.form.user');

        $form->process($user);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:edit.html.'.$this->getEngine(), array(
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
        
        $this->checkPermission($user);

        $form = $this->container->get('fos_user.form.user');

        $process = $form->process($user);
        if ($process) {
            $this->setFlash('fos_user_user_update', 'success');
            $userUrl =  $this->container->get('router')->generate('fos_user_user_show', array('username' => $user->getUsername()));
            return new RedirectResponse($userUrl);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'username'  => $user->getUsername()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->container->get('fos_user.form.user');

        $form->process();

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:new.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Create a user and send a confirmation email
     */
    public function createAction()
    {
        $form = $this->container->get('fos_user.form.user');

        $process = $form->process(null, $this->container->getParameter('fos_user.email.confirmation.enabled'));
        if ($process) {

            $user = $form->getData();

            if ($this->container->getParameter('fos_user.email.confirmation.enabled')) {
                $this->container->get('fos_user.util.mailer')->sendConfirmationEmailMessage($user, $this->getEngine());
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_user_check_confirmation_email';
            } else {
                $this->authenticateUser($user);
                $route = 'fos_user_user_confirmed';
            }

            if ($this->container->has('security.acl.provider')) {
                $provider = $this->container->get('security.acl.provider');
                $acl = $provider->createAcl(ObjectIdentity::fromDomainObject($user));
                $acl->insertObjectAce(UserSecurityIdentity::fromAccount($user), MaskBuilder::MASK_OWNER);
                $provider->updateAcl($acl);
            }

            $this->setFlash('fos_user_user_create', 'success');
            $url = $this->container->get('router')->generate($route);
            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:new.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkConfirmationEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->findUserBy('email', $email);

        $this->setFlash('fos_user_user_confirm', 'success');

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:checkConfirmationEmail.html.'.$this->getEngine(), array(
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

        $this->container->get('fos_user.user_manager')->updateUser($user);
        $this->authenticateUser($user);

        return new RedirectResponse( $this->container->get('router')->generate('fos_user_user_confirmed'));
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();

        $this->setFlash('fos_user_user_confirmed', 'success');
        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:confirmed.html.'.$this->getEngine(), array(
            'user' => $user,
        ));
    }

    /**
     * Delete one user
     */
    public function deleteAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $this->container->get('fos_user.user_manager')->deleteUser($user);
        $this->setFlash('fos_user_user_delete', 'success');

        return new RedirectResponse( $this->container->get('router')->generate('fos_user_user_list'));
    }

    /**
     * Change user password: show form
     */
    public function changePasswordAction()
    {
        $user = $this->getUser();
        $form = $this->container->get('fos_user.form.change_password');
        $form->process($user);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:changePassword.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Change user password: submit form
     */
    public function changePasswordUpdateAction()
    {
        $user = $this->getUser();
        $form = $this->container->get('fos_user.form.change_password');

        $process = $form->process($user);
        if ($process) {
            $this->setFlash('fos_user_user_password', 'success');
            $url =  $this->container->get('router')->generate('fos_user_user_show', array('username' => $user->getUsername()));
            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:changePassword.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Request reset user password: show form
     */
    public function requestResetPasswordAction()
    {
        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:requestResetPassword.html.'.$this->getEngine());
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendResettingEmailAction()
    {
        $user = $this->findUserBy('username', $this->container->get('request')->get('username'));

        if ($user->isPasswordRequestNonExpired($this->getPasswordRequestTtl())) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:User:passwordAlreadyRequested.html.'.$this->getEngine());
        }

        $user->generateConfirmationToken();
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);
        $this->container->get('session')->set('fos_user_send_resetting_email/email', $user->getEmail());
        $this->container->get('fos_user.util.mailer')->sendResettingEmailMessage($user, $this->getEngine());

        return new RedirectResponse( $this->container->get('router')->generate('fos_user_user_check_resetting_email'));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkResettingEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_resetting_email/email');
        $this->container->get('session')->remove('fos_user_send_resetting_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);
        if (empty($user)) {
            return new RedirectResponse( $this->container->get('router')->generate('fos_user_user_request_reset_password'));
        }

        $this->setFlash('fos_user_user_reset', 'success');

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:checkResettingEmail.html.'.$this->getEngine(), array(
            'user' => $user,
        ));
    }

    /**
     * Reset user password: show form
     */
    public function resetPasswordAction($token)
    {
        $user = $this->findUserBy('confirmationToken', $token);

        if ($user->isPasswordRequestNonExpired($this->getPasswordRequestTtl())) {
            return new RedirectResponse( $this->container->get('router')->generate('fos_user_user_request_reset_password'));
        }

        $form = $this->container->get('fos_user.form.reset_password');
        $form->process($user);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:resetPassword.html.'.$this->getEngine(), array(
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

        if ($user->isPasswordRequestNonExpired($this->getPasswordRequestTtl())) {
            return new RedirectResponse( $this->container->get('router')->generate('fos_user_user_request_reset_password'));
        }

        $form = $this->container->get('fos_user.form.reset_password');

        $process = $form->process($user);
        if ($process) {
            $this->authenticateUser($user);

            $this->setFlash('fos_user_user_resetted', 'success');
            $url =  $this->container->get('router')->generate('fos_user_user_show', array('username' => $user->getUsername()));
            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:resetPassword.html.'.$this->getEngine(), array(
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
        $user = $this->container->get('security.context')->getToken()->getUser();
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
            $user = $this->container->get('fos_user.user_manager')->{'findUserBy'.ucfirst($key)}($value);
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
    protected function authenticateUser(UserInterface $user, $reAuthenticate = false)
    {
        $providerKey = $this->container->getParameter('fos_user.provider_key');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        if (true === $reAuthenticate) {
            $token->setAuthenticated(false);
        }

        $this->container->get('security.context')->setToken($token);
    }

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }

    protected function getPasswordRequestTtl()
    {
        return $this->container->getParameter('fos_user.email.resetting_password.token_ttl');
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }
    
    /**
     * Checks if a User is permitted to update the Profile
     */
    protected function checkPermission(UserInterface $user)
    {
        if ($user->getId() !== $this->container->get('security.context')->getToken()->getUser()->getId())
        {
            throw new AccessDeniedException('You are not permitted to update this User.');
        }
    }
}

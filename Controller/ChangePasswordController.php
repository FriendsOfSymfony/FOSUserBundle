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

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

/**
 * Controller managing the password change
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ChangePasswordController extends ContainerAware
{
    public function changePasswordAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $process = $this->getUserFormHandler()->process($user);
        if ($process) {
            $this->setFlash('fos_user_success', 'change_password.flash.success');

            return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
        }

        return $this->renderResponse(
            'FOSUserBundle:ChangePassword:changePassword.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $this->getUserForm()->createView())
        );
    }

    protected function getUser()
    {
        return  $this->container->get('security.context')->getToken()->getUser();
    }

    protected function getUserForm()
    {
        return $this->container->get('fos_user.change_password.form');
    }

    protected function getUserFormHandler()
    {
        return $this->container->get('fos_user.change_password.form.handler');
    }

    protected function renderResponse($template, array $args)
    {
        return $this->container->get('templating')->renderResponse($template, $args);
    }

    protected function generateUrl($route)
    {
        return $this->container->get('router')->generate($route);
    }

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }
}

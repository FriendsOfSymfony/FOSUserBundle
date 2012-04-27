<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form\Handler;

use FOS\UserBundle\Events;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Form\Model\ResetPassword;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResettingFormHandler
{
    protected $request;
    protected $userManager;
    protected $form;
    protected $dispatcher;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager, EventDispatcherInterface $dispatcher)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
    }

    public function getNewPassword()
    {
        return $this->form->getData()->new;
    }

    public function process(UserInterface $user)
    {
        $this->form->setData(new ResetPassword());

        if ('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($user);

                $event = new UserEvent($user);
                $this->dispatcher->dispatch(Events::USER_RESET_PASSWORD, $event);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess(UserInterface $user)
    {
        $user->setPlainPassword($this->getNewPassword());
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
        $this->userManager->updateUser($user);
    }
}

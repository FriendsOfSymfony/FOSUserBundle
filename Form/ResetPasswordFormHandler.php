<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Form\ResetPassword;

class ResetPasswordFormHandler
{
    protected $request;
    protected $userManager;
    protected $form;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
    }

    public function getNewPassword()
    {
        return $this->form->getData()->new;
    }

    public function process(UserInterface $user)
    {
        $this->form->setData(new ResetPassword($user));

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $user->setPlainPassword($this->getNewPassword());
                $user->setConfirmationToken(null);
                $user->setEnabled(true);
                $this->userManager->updateUser($user);

                return true;
            }
        }

        return false;
    }
}

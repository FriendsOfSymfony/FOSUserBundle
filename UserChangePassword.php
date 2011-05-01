<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
//use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
//use Symfony\Component\Console\Input\InputArgument;
//use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Output\OutputInterface;

class UserChangePassword
{
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function changePassword($username, $password)
    {
        $user = $this->userManager->findUserByUsername($username);
        
        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }
        $user->setPlainPassword($password);
        $this->userManager->updateUser($user);
    }

}

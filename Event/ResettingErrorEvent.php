<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ResettingErrorEvent extends FormUserErrorEvent
{
    private $token;

    public function __construct(UserInterface $user, Request $request, FormInterface $form, $token)
    {
        parent::__construct($user, $request, $form);
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}

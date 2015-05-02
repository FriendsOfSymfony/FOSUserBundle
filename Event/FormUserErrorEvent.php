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

class FormUserErrorEvent extends GetResponseUserEvent
{
    private $form;

    public function __construct(UserInterface $user, Request $request, FormInterface $form)
    {
        parent::__construct($user, $request);
        $this->form = $form;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}

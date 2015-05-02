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

use FOS\UserBundle\Model\GroupInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class FormGroupErrorEvent extends GetResponseGroupEvent
{
    private $form;

    public function __construct(GroupInterface $group, Request $request, FormInterface $form)
    {
        parent::__construct($group, $request);
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

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

use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class GroupFormHandler
{
    protected $request;
    protected $groupManager;
    protected $form;

    public function __construct(FormInterface $form, Request $request, GroupManagerInterface $groupManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->groupManager = $groupManager;
    }

    public function process(GroupInterface $group = null)
    {
        if (null === $group) {
            $group = $this->groupManager->createGroup('');
        }

        $this->form->setData($group);

        if ('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($group);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess(GroupInterface $group)
    {
        $this->groupManager->updateGroup($group);
    }
}

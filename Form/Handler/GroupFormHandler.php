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

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;

class GroupFormHandler
{
    protected $request;
    protected $groupManager;
    protected $form;

    public function __construct(Form $form, Request $request, GroupManagerInterface $groupManager)
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

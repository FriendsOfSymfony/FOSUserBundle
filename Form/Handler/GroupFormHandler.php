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

class GroupFormHandler extends RequestFormHandler
{
    protected $groupManager;
    protected $form;

    public function __construct(FormInterface $form, GroupManagerInterface $groupManager)
    {
        $this->form = $form;
        $this->groupManager = $groupManager;
    }

    public function process(GroupInterface $group = null)
    {
        if (null === $group) {
            $group = $this->groupManager->createGroup('');
        }

        $this->form->setData($group);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $this->form->handleRequest($request);

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

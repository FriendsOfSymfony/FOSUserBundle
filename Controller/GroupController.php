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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RESTful controller managing group CRUD
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class GroupController extends ContainerAware
{
    public function listAction()
    {
        $groups = $this->getGroupManager()->findGroups();

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Group:list.html.'.$this->getEngine(), array('groups' => $groups)
        );
    }

    public function showAction($name)
    {
        $group = $this->getGroupManager()->findGroupByName($name);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('No group found with name "%s".', $name));
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Group:show.html.'.$this->getEngine(), array('group' => $group)
        );
    }

    public function newAction()
    {
        $group = $this->getGroupManager()->createGroup('');

        $form = $this->container->get('fos_user.group.form');
        $formHandler = $this->container->get('fos_user.group.form.handler');

        $process = $formHandler->process($group);
        if ($process) {
            $this->setFlash('fos_user_success', 'group.flash.created');
            $url = $this->container->get('router')->generate('fos_user_group_show',
                array('name' => $group->getName())
            );

            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:new.html.'.$this->getEngine(),
            array('form' => $form->createview())
        );
    }

    public function editAction($name)
    {
        $group = $this->getGroupManager()->findGroupByName($name);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('No group found with name "%s".', $name));
        }

        $form = $this->container->get('fos_user.group.form');
        $formHandler = $this->container->get('fos_user.group.form.handler');

        $process = $formHandler->process($group);
        if ($process) {
            $this->setFlash('fos_user_success', 'group.flash.updated');
            $url =  $this->container->get('router')->generate('fos_user_group_show',
                array('group' => $group->getName())
            );

            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:edit.html.'.$this->getEngine(), array(
            'form'      => $form->createview(),
            'group'  => $group,
        ));
    }

    public function deleteAction($name)
    {
        $group = $this->getGroupManager()
            ->findGroupByName($name);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('No group found with name "%s".', $name));
        }

        if ($this->container->get('request')->getMethod() == 'DELETE') {
            $this->getGroupManager()->deleteGroup($group);
            $this->setFlash('fos_user_success', 'group.flash.deleted');

            return new RedirectResponse($this->container->get('router')->generate('fos_user_group_list'));
        }

        return $this->renderResponse('FOSUserBundle:Group:delete.html.'.$this->getEngine(), array(
            'group' => $group,
        ));
    }

    protected function getGroupManager()
    {
        return $this->container->get('fos_user.group_manager');
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }
}
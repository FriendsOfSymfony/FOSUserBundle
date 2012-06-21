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
        $groups = $this->getGroupManager()
            ->findGroups();

        return $this->renderResponse(
            'FOSUserBundle:Group:list.html.'.$this->getEngine(),
            array('groups' => $groups)
        );
    }

    public function showAction($groupname)
    {
        $group = $this->getGroupManager()
            ->findGroupByName($groupname);

        return $this->renderResponse(
            'FOSUserBundle:Group:show.html.'.$this->getEngine(),
            array('group' => $group)
        );
    }

    public function newAction()
    {
        $group = $this->getGroupManager()
            ->createGroup('');

        $process = $this->getGroupFormHandler()->process($group);
        if ($process) {
            $this->setFlash('fos_user_success', 'group.flash.created');

            return new RedirectResponse($this->generateUrl('fos_user_group_show', array('groupname' => $group->getName())));
        }

        return $this->renderResponse('FOSUserBundle:Group:new.html.'.$this->getEngine(), array(
            'form' => $this->getGroupForm->createview(),
        ));
    }

    public function editAction($groupname)
    {
        $group = $this->getGroupManager()
            ->findGroupByName($groupname);

        $process = $this->getGroupFormHandler()->process($group);
        if ($process) {
            $this->setFlash('fos_user_success', 'group.flash.updated');

            return new RedirectResponse($this->generateUrl('fos_user_group_show', array('groupname' => $group->getName())));
        }

        return $this->renderResponse(
            'FOSUserBundle:Group:edit.html.'.$this->getEngine(), array(
                'form'      => $this->getGroupForm->createview(),
                'groupname'  => $group->getName(),
            )
        );
    }

    public function deleteAction($groupname)
    {
        $group = $this->getGroupManager()
            ->findGroupByName($groupname);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('No group found with name "%s".', $groupname));
        }

        if ($this->getRequest()->getMethod() == 'DELETE') {
            $this->getGroupManager()->deleteGroup($group);
            $this->setFlash('fos_user_success', 'group.flash.deleted');

            return new RedirectResponse($this->generateUrl('fos_user_group_list'));
        }

        return $this->renderResponse('FOSUserBundle:Group:delete.html.'.$this->getEngine(), array(
            'group' => $group,
        ));
    }

    protected function getGroupForm()
    {
        return $this->container->get('fos_user.group.form');
    }

    protected function getGroupFormHandler()
    {
        return $this->container->get('fos_user.group.form.handler');
    }

    protected function getGroupManager()
    {
        return $this->container->get('fos_user.group_manager');
    }

    protected function generateUrl($route, array $args = null)
    {
        return $this->container->get('router')->generate($route, $args);
    }

    protected function getRequest()
    {
        return $this->container->get('request');
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }

    protected function renderResponse($template, array $args)
    {
        return $this->container->get('templating')->renderResponse($template, $args);
    }

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }
}

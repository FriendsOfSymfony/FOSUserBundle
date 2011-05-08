<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Christophe Coevoet <stof@notk.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

use FOS\UserBundle\Model\Group;

/**
 * RESTful controller managing group CRUD
 */
class GroupController extends ContainerAware
{
    /**
     * Show all groups
     */
    public function listAction()
    {
        $groups = $this->container->get('fos_user.group_manager')->findGroups();

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:list.html.'.$this->getEngine(), array('groups' => $groups));
    }

    /**
     * Show one group
     */
    public function showAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:show.html.'.$this->getEngine(), array('group' => $group));
    }

    /**
     * Edit one group, show the edit form
     */
    public function editAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $form = $this->container->get('fos_user.form.group');
        $formHandler = $this->container->get('fos_user.form.handler.group');

        $process = $formHandler->process($group);
        if ($process) {
            $this->setFlash('success', 'fos_user_group_update');
            $groupUrl =  $this->container->get('router')->generate('fos_user_group_show', array('groupname' => $group->getName()));
            return new RedirectResponse($groupUrl);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:edit.html.'.$this->getEngine(), array(
            'form'      => $form->createview(),
            'groupname'  => $group->getName()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->container->get('fos_user.form.group');
        $formHandler = $this->container->get('fos_user.form.handler.group');

        $process = $formHandler->process();
        if ($process) {
            $this->container->get('session')->setFlash('success', 'fos_user_group_update');
            $parameters = array('groupname' => $form->getData('group')->getName());
            $url = $this->container->get('router')->generate('fos_user_group_show', $parameters);
            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:new.html.'.$this->getEngine(), array(
            'form' => $form->createview()
        ));
    }

    /**
     * Delete one group
     */
    public function deleteAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $this->container->get('fos_user.group_manager')->deleteGroup($group);
        $this->setFlash('success', 'fos_user_group_delete');

        return new RedirectResponse( $this->container->get('router')->generate('fos_user_group_list'));
    }

    /**
     * Find a group by a specific property
     *
     * @param string $key property name
     * @param mixed $value property value
     * @throws NotFoundException if user does not exist
     * @return Group
     */
    protected function findGroupBy($key, $value)
    {
        if (!empty($value)) {
            $group = $this->container->get('fos_user.group_manager')->{'findGroupBy'.ucfirst($key)}($value);
        }

        if (empty($group)) {
            throw new NotFoundHttpException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }

    protected function setFlash($key, $value)
    {
        $this->container->get('session')->setFlash($key, $value);
    }
}

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

        return $this->container->get('templating')->renderResponse($this->getTemplateBundle() . ':Group:list.html.'.$this->getEngine(), array('groups' => $groups));
    }

    /**
     * Show one group
     */
    public function showAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);

        return $this->container->get('templating')->renderResponse($this->getTemplateBundle() . ':Group:show.html.'.$this->getEngine(), array('group' => $group));
    }

    /**
     * Edit one group, show the edit form
     */
    public function editAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $form = $this->container->get('fos_user.form.group');

        $form->process($group);

        return $this->container->get('templating')->renderResponse($this->getTemplateBundle() . ':Group:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'groupname'  => $group->getName()
        ));
    }

    /**
     * Update a group
     */
    public function updateAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $form = $this->container->get('fos_user.form.group');

        $process = $form->process($group);
        if ($process) {
            $this->setFlash('fos_user_group_update', 'success');
            $groupUrl =  $this->container->get('router')->generate('fos_user_group_show', array('groupname' => $group->getName()));
            return new RedirectResponse($groupUrl);
        }

        return $this->container->get('templating')->renderResponse($this->getTemplateBundle() . ':Group:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'groupname'  => $group->getName()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->container->get('fos_user.form.group');

        $form->process();

        return $this->container->get('templating')->renderResponse($this->getTemplateBundle() . ':Group:new.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Create a group
     */
    public function createAction()
    {
        $form = $this->container->get('fos_user.form.group');

        $process = $form->process();
        if ($process) {
            $this->container->get('session')->setFlash('fos_user_group_update', 'success');
            $parameters = array('groupname' => $form->getData('group')->getName());
            $url = $this->container->get('router')->generate('fos_user_group_show', $parameters);
            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse($this->getTemplateBundle() . ':Group:new.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Delete one group
     */
    public function deleteAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $this->container->get('fos_user.group_manager')->deleteGroup($group);
        $this->setFlash('fos_user_group_delete', 'success');

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

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }

    protected function getTemplateBundle()
    {
        return $this->container->getParameter('fos_user.template.bundle');
    }
}

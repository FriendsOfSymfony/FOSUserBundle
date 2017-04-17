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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class BaseContainerAware extends ContainerAware
{

    protected $templateBundle;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->templateBundle = $container->getParameter('mardraze.user.template_bundle');
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function addFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->add($action, $value);
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }
}

<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Provides backward compatibility
 *
 * @author Andrey Mindubaev <andrey@mindubaev.ru>
 */
class BackwardCompatibilityPass implements CompilerPassInterface
{
    /**
     * @var array
     */
    private $bcFactoryForms = array(
        "fos_user.change_password.form",
        "fos_user.group.form",
        "fos_user.profile.form",
        "fos_user.registration.form",
        "fos_user.resetting.form"
    );

    private $bcScopeRequest = array(
        "fos_user.change_password.form.handler.default",
        "fos_user.profile.form.handler.default",
        "fos_user.registration.form.handler.default",
        "fos_user.resetting.form.handler.default",
        "fos_user.group.form.handler.default",
    );

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition("fos_user.entity_manager")) {
            $definition = $container->getDefinition("fos_user.entity_manager");

            $this->setFactory($definition, "doctrine", "getManager");
        }
        if ($container->hasDefinition("fos_user.document_manager")) {
            $definition = $container->getDefinition("fos_user.document_manager");

            switch ($definition->getClass()) {
                case 'Doctrine\ODM\CouchDB\DocumentManager':
                    $this->setFactory($definition, "doctrine_couchdb", "getManager");
                    break;
                case 'Doctrine\ODM\MongoDB\DocumentManager':
                    $this->setFactory($definition, "doctrine_mongodb", "getManager");
                    break;
            }
        }
        foreach ($this->bcFactoryForms as $service) {
            $definition = $container->getDefinition($service);

            $this->setFactory($definition, "form.factory", "createNamed");
            $this->setScopeRequest($definition);
        }
        foreach ($this->bcScopeRequest as $service) {
            $definition = $container->getDefinition($service);

            $this->setScopeRequest($definition);
        }
    }

    /**
     * Set scope="request" to definition
     *
     * @param Definition $definition Definition
     */
    protected function setScopeRequest(Definition $definition)
    {
        if (method_exists($definition, "setScope")) {
            $definition->setScope("request");
        }
    }

    protected function setFactory(Definition $definition, $service, $method)
    {
        if (method_exists($definition, "setFactory")) {
            $definition->setFactory(
                array(new Reference($service), $method)
            );
        } else {
            $definition
                ->setFactoryService($service)
                ->setFactoryMethod($method);
        }
    }
}

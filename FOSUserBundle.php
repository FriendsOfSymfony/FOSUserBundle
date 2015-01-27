<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use FOS\UserBundle\DependencyInjection\Compiler\ValidationPass;
use FOS\UserBundle\DependencyInjection\Compiler\RegisterMappingsPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Doctrine\Bundle\CouchDBBundle\DependencyInjection\Compiler\DoctrineCouchDBMappingsPass;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class FOSUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ValidationPass());

        $this->addRegisterMappingsPass($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        // the base class is only available since symfony 2.3
        $symfonyVersion = class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass');

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'FOS\UserBundle\Model',
        );

        if ($symfonyVersion && class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('fos_user.model_manager_name'), 'fos_user.backend_type_orm'));
        } else {
            $container->addCompilerPass(RegisterMappingsPass::createOrmMappingDriver($mappings));
        }

        if ($symfonyVersion && class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, array('fos_user.model_manager_name'), 'fos_user.backend_type_mongodb'));
        } else {
            $container->addCompilerPass(RegisterMappingsPass::createMongoDBMappingDriver($mappings));
        }

        if ($symfonyVersion && class_exists('Doctrine\Bundle\CouchDBBundle\DependencyInjection\Compiler\DoctrineCouchDBMappingsPass')) {
            $container->addCompilerPass(DoctrineCouchDBMappingsPass::createXmlMappingDriver($mappings, array('fos_user.model_manager_name'), 'fos_user.backend_type_couchdb'));
        } else {
            $container->addCompilerPass(RegisterMappingsPass::createCouchDBMappingDriver($mappings));
        }
    }
}

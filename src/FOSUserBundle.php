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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Doctrine\DBAL\Types\Type;
use FOS\UserBundle\DependencyInjection\Compiler\CheckForSessionPass;
use FOS\UserBundle\DependencyInjection\Compiler\InjectRememberMeServicesPass;
use FOS\UserBundle\DependencyInjection\Compiler\InjectUserCheckerPass;
use FOS\UserBundle\DependencyInjection\Compiler\ValidationPass;
use FOS\UserBundle\Doctrine\SerializedArrayType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
final class FOSUserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new ValidationPass());
        $container->addCompilerPass(new InjectUserCheckerPass());
        $container->addCompilerPass(new InjectRememberMeServicesPass());
        $container->addCompilerPass(new CheckForSessionPass());

        $this->addRegisterMappingsPass($container);
    }

    public function boot(): void
    {
        if (class_exists(Type::class) && !Type::hasType(SerializedArrayType::NAME)) {
            Type::addType(SerializedArrayType::NAME, SerializedArrayType::class);
        }
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping') => 'FOS\UserBundle\Model',
        ];

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, ['fos_user.model_manager_name'], 'fos_user.backend_type_orm', [], true));
        }

        if (class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, ['fos_user.model_manager_name'], 'fos_user.backend_type_mongodb'));
        }
    }
}

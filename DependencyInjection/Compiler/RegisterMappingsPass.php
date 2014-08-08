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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * Forward compatibility class in case FOSUserBundle is used with older
 * versions of Symfony2 or the doctrine bundles that do not provide the
 * register mappings compiler pass yet.
 *
 * @deprecated Compatibility class to make the bundle work with Symfony < 2.3.
 * To be removed when this bundle drops support for Symfony < 2.3
 *
 * @author David Buchmann <david@liip.ch>
 */
class RegisterMappingsPass implements CompilerPassInterface
{
    private $driver;
    private $driverPattern;
    private $namespaces;
    private $enabledParameter;
    private $fallbackManagerParameter;
    private $configurationPattern;
    private $registerAliasMethodName;
    private $aliasMap;

    public function __construct($driver, $driverPattern, $namespaces, $enabledParameter, $fallbackManagerParameter, $configurationPattern = '', $registerAliasMethodName = '', array $aliasMap = array())
    {
        $this->driver = $driver;
        $this->driverPattern = $driverPattern;
        $this->namespaces = $namespaces;
        $this->enabledParameter = $enabledParameter;
        $this->fallbackManagerParameter = $fallbackManagerParameter;
        if (count($aliasMap) && (!$configurationPattern || !$registerAliasMethodName)) {
            throw new \InvalidArgumentException('configurationPattern and registerAliasMethodName are required to register namespace alias');
        }
        $this->configurationPattern = $configurationPattern;
        $this->registerAliasMethodName = $registerAliasMethodName;
        $this->aliasMap = $aliasMap;
    }

    /**
     * Register mappings and aliases with the metadata drivers.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter($this->enabledParameter)) {
            return;
        }

        $chainDriverDefService = $this->getChainDriverServiceName($container);
        // Definition for a Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain
        $chainDriverDef = $container->getDefinition($chainDriverDefService);
        foreach ($this->namespaces as $namespace) {
            $chainDriverDef->addMethodCall('addDriver', array($this->driver, $namespace));
        }

        if (!count($this->aliasMap)) {
            return;
        }

        $configurationServiceName = $this->getConfigurationServiceName($container);
        // Definition of the Doctrine\...\Configuration class specific to the Doctrine flavour.
        $configurationServiceDefinition = $container->getDefinition($configurationServiceName);
        foreach ($this->aliasMap as $alias => $namespace) {
           $configurationServiceDefinition->addMethodCall($this->registerAliasMethodName, array($alias, $namespace));
        }
    }

    protected function getChainDriverServiceName(ContainerBuilder $container)
    {
        foreach (array('fos_user.model_manager_name', $this->fallbackManagerParameter) as $param) {
            if ($container->hasParameter($param)) {
                $name = $container->getParameter($param);
                if ($name) {
                    return sprintf($this->driverPattern, $name);
                }
            }
        }

        throw new ParameterNotFoundException('None of the managerParameters resulted in a valid name');
    }

    public static function createOrmMappingDriver(array $mappings, array $aliasMap = array())
    {
        $arguments = array($mappings, '.orm.xml');
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $driver = new Definition('Doctrine\ORM\Mapping\Driver\XmlDriver', array($locator));

        return new RegisterMappingsPass($driver, 'doctrine.orm.%s_metadata_driver', $mappings, 'fos_user.backend_type_orm', 'doctrine.default_entity_manager', 'doctrine.orm.%s_configuration', 'addEntityNamespace', $aliasMap);
    }

    public static function createMongoDBMappingDriver($mappings, array $aliasMap = array())
    {
        $arguments = array($mappings, '.mongodb.xml');
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $driver = new Definition('Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver', array($locator));

        return new RegisterMappingsPass($driver, 'doctrine_mongodb.odm.%s_metadata_driver', $mappings, 'fos_user.backend_type_mongodb', 'doctrine_mongodb.odm.default_document_manager', 'doctrine_mongodb.odm.%s_configuration', 'addDocumentNamespace', $aliasMap);
    }

    public static function createCouchDBMappingDriver($mappings, array $aliasMap = array())
    {
        $arguments = array($mappings, '.couchdb.xml');
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $driver = new Definition('Doctrine\ODM\CouchDB\Mapping\Driver\XmlDriver', array($locator));

        return new RegisterMappingsPass($driver, 'doctrine_couchdb.odm.%s_metadata_driver', $mappings, 'fos_user.backend_type_couchdb', 'doctrine_couchdb.default_document_manager', 'doctrine_couchdb.odm.%s_configuration', 'addDocumentNamespace', $aliasMap);
    }
}

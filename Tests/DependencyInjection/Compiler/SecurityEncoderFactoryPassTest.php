<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\DependencyInjection\Compiler;

use FOS\UserBundle\DependencyInjection\Compiler\SecurityEncoderFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SecurityEncoderFactoryPassTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $pass;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->pass = new SecurityEncoderFactoryPass();
    }

    public function testShouldComposeAlias()
    {
        $this->container->setDefinition('security.encoder_factory.real', new Definition());
        $this->container->setAlias('security.encoder_factory', 'security.encoder_factory.real');

        $this->pass->process($this->container);

        $this->assertServiceHasAlias('security.encoder_factory.real', 'fos_user.encoder_factory.parent');
        $this->assertFalse($this->container->getAlias('fos_user.encoder_factory.parent')->isPublic());
        $this->assertServiceHasAlias('fos_user.encoder_factory', 'security.encoder_factory');
    }

    public function testShouldComposeDefinition()
    {
        $this->container->setDefinition('security.encoder_factory', $originalDefinition = new Definition());

        $this->pass->process($this->container);

        $newDefinition = $this->container->getDefinition('fos_user.encoder_factory.parent');
        $this->assertFalse($newDefinition->isPublic());
        $this->assertSame($originalDefinition, $newDefinition);

        $this->assertServiceHasAlias('fos_user.encoder_factory', 'security.encoder_factory');
    }

    private function assertServiceHasAlias($serviceId, $aliasId)
    {
        $this->assertEquals($serviceId, (string) $this->container->getAlias($aliasId), sprintf('Service "%s" has alias "%s"', $serviceId, $aliasId));
    }
}

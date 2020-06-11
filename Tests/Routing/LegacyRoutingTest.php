<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * @group legacy
 */
class LegacyRoutingTest extends TestCase
{
    /**
     * @dataProvider loadRoutingProvider
     *
     * @param string $routeName
     * @param string $path
     */
    public function testLegacyGroupRouting($routeName, $path, array $methods)
    {
        $locator = new FileLocator();
        $loader = new XmlFileLoader($locator);

        $collection = new RouteCollection();
        $subCollection = $loader->load(__DIR__.'/../../Resources/config/routing/group.xml');
        $subCollection->addPrefix('/group');
        $collection->addCollection($subCollection);

        $route = $collection->get($routeName);
        $this->assertNotNull($route, sprintf('The route "%s" should exists', $routeName));
        $this->assertSame($path, $route->getPath());
        $this->assertSame($methods, $route->getMethods());
    }

    /**
     * @return array
     */
    public function loadRoutingProvider()
    {
        return [
            ['fos_user_group_list', '/group/list', ['GET']],
            ['fos_user_group_new', '/group/new', ['GET', 'POST']],
            ['fos_user_group_show', '/group/{groupName}', ['GET']],
            ['fos_user_group_edit', '/group/{groupName}/edit', ['GET', 'POST']],
            ['fos_user_group_delete', '/group/{groupName}/delete', ['GET']],
        ];
    }
}

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

class RoutingTest extends TestCase
{
    /**
     * @dataProvider loadRoutingProvider
     *
     * @param string $routeName
     * @param string $path
     */
    public function testLoadRouting($routeName, $path, array $methods)
    {
        $locator = new FileLocator();
        $loader = new XmlFileLoader($locator);

        $collection = new RouteCollection();
        $collection->addCollection($loader->load(__DIR__.'/../../Resources/config/routing/change_password.xml'));
        $subCollection = $loader->load(__DIR__.'/../../Resources/config/routing/group.xml');
        $subCollection->addPrefix('/group');
        $collection->addCollection($subCollection);
        $subCollection = $loader->load(__DIR__.'/../../Resources/config/routing/profile.xml');
        $subCollection->addPrefix('/profile');
        $collection->addCollection($subCollection);
        $subCollection = $loader->load(__DIR__.'/../../Resources/config/routing/registration.xml');
        $subCollection->addPrefix('/register');
        $collection->addCollection($subCollection);
        $subCollection = $loader->load(__DIR__.'/../../Resources/config/routing/resetting.xml');
        $subCollection->addPrefix('/resetting');
        $collection->addCollection($subCollection);
        $collection->addCollection($loader->load(__DIR__.'/../../Resources/config/routing/security.xml'));

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
            ['fos_user_change_password', '/change-password', ['GET', 'POST']],

            ['fos_user_group_list', '/group/list', ['GET']],
            ['fos_user_group_new', '/group/new', ['GET', 'POST']],
            ['fos_user_group_show', '/group/{groupName}', ['GET']],
            ['fos_user_group_edit', '/group/{groupName}/edit', ['GET', 'POST']],
            ['fos_user_group_delete', '/group/{groupName}/delete', ['GET']],

            ['fos_user_profile_show', '/profile/', ['GET']],
            ['fos_user_profile_edit', '/profile/edit', ['GET', 'POST']],

            ['fos_user_registration_register', '/register/', ['GET', 'POST']],
            ['fos_user_registration_check_email', '/register/check-email', ['GET']],
            ['fos_user_registration_confirm', '/register/confirm/{token}', ['GET']],
            ['fos_user_registration_confirmed', '/register/confirmed', ['GET']],

            ['fos_user_resetting_request', '/resetting/request', ['GET']],
            ['fos_user_resetting_send_email', '/resetting/send-email', ['POST']],
            ['fos_user_resetting_check_email', '/resetting/check-email', ['GET']],
            ['fos_user_resetting_reset', '/resetting/reset/{token}', ['GET', 'POST']],

            ['fos_user_security_login', '/login', ['GET', 'POST']],
            ['fos_user_security_check', '/login_check', ['POST']],
            ['fos_user_security_logout', '/logout', ['GET', 'POST']],
        ];
    }
}

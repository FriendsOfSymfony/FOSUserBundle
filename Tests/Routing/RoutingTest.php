<?php

namespace FOS\UserBundle\Tests\Routing;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class RoutingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider loadRoutingProvider
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
        $this->assertEquals($path, $route->getPath());
        $this->assertEquals($methods, $route->getMethods());
    }

    public function loadRoutingProvider()
    {
        return array(
            array('fos_user_change_password', '/change-password', array('GET', 'POST')),

            array('fos_user_group_list', '/group/list', array('GET')),
            array('fos_user_group_new', '/group/new', array('GET', 'POST')),
            array('fos_user_group_show', '/group/{groupName}', array('GET')),
            array('fos_user_group_edit', '/group/{groupName}/edit', array('GET', 'POST')),
            array('fos_user_group_delete', '/group/{groupName}/delete', array('GET')),

            array('fos_user_profile_show', '/profile/', array('GET')),
            array('fos_user_profile_edit', '/profile/edit', array('GET', 'POST')),

            array('fos_user_registration_register', '/register/', array('GET', 'POST')),
            array('fos_user_registration_check_email', '/register/check-email', array('GET')),
            array('fos_user_registration_confirm', '/register/confirm/{token}', array('GET')),
            array('fos_user_registration_confirmed', '/register/confirmed', array('GET')),

            array('fos_user_resetting_request', '/resetting/request', array('GET')),
            array('fos_user_resetting_send_email', '/resetting/send-email', array('POST')),
            array('fos_user_resetting_check_email', '/resetting/check-email', array('GET')),
            array('fos_user_resetting_reset', '/resetting/reset/{token}', array('GET', 'POST')),

            array('fos_user_security_login', '/login', array('GET', 'POST')),
            array('fos_user_security_check', '/login_check', array('POST')),
            array('fos_user_security_logout', '/logout', array('GET')),
        );
    }
}

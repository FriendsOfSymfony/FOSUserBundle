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
use Symfony\Component\HttpKernel\Config\FileLocator as KernelFileLocator;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class RoutingTest extends TestCase
{
    private const BUNDLE_PREFIX = '@FOSUserBundle/';
    private const SOURCE_DIR = __DIR__.'/../../src';

    public function testLoadAllRouting(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('locateResource')->willReturnCallback(static function (string $resource): string {
            self::assertStringStartsWith(self::BUNDLE_PREFIX, $resource);

            return self::SOURCE_DIR.'/'.substr($resource, \strlen(self::BUNDLE_PREFIX));
        });

        $loader = new PhpFileLoader(new KernelFileLocator($kernel));
        $collection = $loader->load(self::SOURCE_DIR.'/Resources/config/routing/all.php');

        $expectedRouteNames = array_column(self::loadRoutingProvider(), 0);
        $actualRouteNames = array_keys($collection->all());
        sort($expectedRouteNames);
        sort($actualRouteNames);

        self::assertSame($expectedRouteNames, $actualRouteNames);
    }

    /**
     * @dataProvider loadRoutingProvider
     *
     * @param string   $routeName
     * @param string   $path
     * @param string[] $methods
     */
    public function testLoadRouting($routeName, $path, array $methods)
    {
        $locator = new FileLocator();
        $loader = new PhpFileLoader($locator);

        $collection = new RouteCollection();
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/change_password.php'));
        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/profile.php');
        $subCollection->addPrefix('/profile');
        $collection->addCollection($subCollection);
        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/registration.php');
        $subCollection->addPrefix('/register');
        $collection->addCollection($subCollection);
        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/resetting.php');
        $subCollection->addPrefix('/resetting');
        $collection->addCollection($subCollection);
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/security.php'));

        $route = $collection->get($routeName);
        $this->assertNotNull($route, sprintf('The route "%s" should exists', $routeName));
        $this->assertSame($path, $route->getPath());
        $this->assertSame($methods, $route->getMethods());
    }

    /**
     * @dataProvider loadRoutingProvider
     *
     * @group legacy
     *
     * @param string   $routeName
     * @param string   $path
     * @param string[] $methods
     */
    public function testLoadRoutingLegacy($routeName, $path, array $methods)
    {
        if (!class_exists(XmlFileLoader::class)) {
            $this->markTestSkipped('XML routing files are not supported on Symfony 8.');
        }

        $locator = new FileLocator();
        $loader = new XmlFileLoader($locator);

        $collection = new RouteCollection();
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/change_password.xml'));
        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/profile.xml');
        $subCollection->addPrefix('/profile');
        $collection->addCollection($subCollection);
        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/registration.xml');
        $subCollection->addPrefix('/register');
        $collection->addCollection($subCollection);
        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/resetting.xml');
        $subCollection->addPrefix('/resetting');
        $collection->addCollection($subCollection);
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/security.xml'));

        $route = $collection->get($routeName);
        $this->assertNotNull($route, sprintf('The route "%s" should exists', $routeName));
        $this->assertSame($path, $route->getPath());
        $this->assertSame($methods, $route->getMethods());
    }

    /**
     * @return iterable<array{string, string, string[]}>
     */
    public static function loadRoutingProvider(): iterable
    {
        return [
            ['fos_user_change_password', '/change-password', ['GET', 'POST']],

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

    /**
     * @dataProvider provideRouteFiles
     *
     * @group legacy
     */
    public function testLegacyFileConsistency(string $filename): void
    {
        if (!class_exists(XmlFileLoader::class)) {
            $this->markTestSkipped('XML routing files are not supported on Symfony 8.');
        }

        $locator = new FileLocator();

        $phpLoader = new PhpFileLoader($locator);
        $xmlLoader = new XmlFileLoader($locator);

        $phpCollection = $phpLoader->load(__DIR__.\sprintf('/../../src/Resources/config/routing/%s.php', $filename));
        $xmlCollection = $xmlLoader->load(__DIR__.\sprintf('/../../src/Resources/config/routing/%s.xml', $filename));

        $this->assertEquals($phpCollection->all(), $xmlCollection->all());
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideRouteFiles(): iterable
    {
        yield ['profile'];
        yield ['registration'];
        yield ['resetting'];
        yield ['security'];
    }
}
// @php-cs-fixer-ignore php_unit_strict We intentionally use a non-strict comparison to verify that loaded routes are equivalent in the consistency test.

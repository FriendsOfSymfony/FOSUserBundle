<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\Controller\SecurityController;
use FOS\UserBundle\EventListener\LastLoginListener;
use FOS\UserBundle\Security\EmailProvider;
use FOS\UserBundle\Security\EmailUserProvider;
use FOS\UserBundle\Security\LoginManager;
use FOS\UserBundle\Security\LoginManagerInterface;
use FOS\UserBundle\Security\UserChecker;
use FOS\UserBundle\Security\UserProvider;
use Psr\Container\ContainerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('fos_user.security.interactive_login_listener.class', LastLoginListener::class);
    $parameters->set('fos_user.security.login_manager.class', LoginManager::class);

    $services->set('fos_user.security.interactive_login_listener', '%fos_user.security.interactive_login_listener.class%')
        ->args([service('fos_user.user_manager')])
        ->tag('kernel.event_subscriber');

    $services->set('fos_user.security.login_manager', '%fos_user.security.login_manager.class%')
        ->args([
            service('security.token_storage'),
            service('security.user_checker'),
            service('security.authentication.session_strategy'),
            service('request_stack'),
            null,
        ]);

    $services->alias(LoginManagerInterface::class, 'fos_user.security.login_manager')
        ->private();

    $services->set('fos_user.user_provider.username', UserProvider::class)
        ->private()
        ->args([service('fos_user.user_manager')]);

    $services->set('fos_user.user_provider.username_email', EmailUserProvider::class)
        ->private()
        ->args([service('fos_user.user_manager')]);

    $services->set('fos_user.user_provider.email', EmailProvider::class)
        ->private()
        ->args([service('fos_user.user_manager')]);

    $services->set('fos_user.security.controller', SecurityController::class)
        ->public()
        ->args([
            service('security.authentication_utils'),
            service('security.csrf.token_manager')->nullOnInvalid(),
        ])
        ->call('setContainer', [service(ContainerInterface::class)])
        ->tag('container.service_subscriber');

    $services->alias(SecurityController::class, 'fos_user.security.controller')
        ->public();

    $services->set('fos_user.user_checker', UserChecker::class)
        ->private();
};

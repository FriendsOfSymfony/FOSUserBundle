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

use FOS\UserBundle\Command\ActivateUserCommand;
use FOS\UserBundle\Command\ChangePasswordCommand;
use FOS\UserBundle\Command\CreateUserCommand;
use FOS\UserBundle\Command\DeactivateUserCommand;
use FOS\UserBundle\Command\DemoteUserCommand;
use FOS\UserBundle\Command\PromoteUserCommand;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.command.activate_user', ActivateUserCommand::class)
        ->args([service('fos_user.util.user_manipulator')])
        ->tag('console.command', ['command' => 'fos:user:activate']);

    $services->set('fos_user.command.change_password', ChangePasswordCommand::class)
        ->args([service('fos_user.util.user_manipulator')])
        ->tag('console.command', ['command' => 'fos:user:change-password']);

    $services->set('fos_user.command.create_user', CreateUserCommand::class)
        ->args([service('fos_user.util.user_manipulator')])
        ->tag('console.command', ['command' => 'fos:user:create']);

    $services->set('fos_user.command.deactivate_user', DeactivateUserCommand::class)
        ->args([service('fos_user.util.user_manipulator')])
        ->tag('console.command', ['command' => 'fos:user:deactivate']);

    $services->set('fos_user.command.demote_user', DemoteUserCommand::class)
        ->args([service('fos_user.util.user_manipulator')])
        ->tag('console.command', ['command' => 'fos:user:demote']);

    $services->set('fos_user.command.promote_user', PromoteUserCommand::class)
        ->args([service('fos_user.util.user_manipulator')])
        ->tag('console.command', ['command' => 'fos:user:promote']);
};

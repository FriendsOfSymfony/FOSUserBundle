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

use FOS\UserBundle\Mailer\NoopMailer;
use FOS\UserBundle\Mailer\TwigSymfonyMailer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('fos_user.resetting.email.template', '@FOSUser/Resetting/email.txt.twig');
    $parameters->set('fos_user.registration.confirmation.template', '@FOSUser/Registration/email.txt.twig');
    $parameters->set('fos_user.registration.confirmation.from_address', ['address' => 'no-registration@acme.com', 'sender_name' => 'Acme Ltd']);
    $parameters->set('fos_user.resetting.email.from_address', ['address' => 'no-resetting@acme.com', 'sender_name' => 'Acme Ltd']);

    $services->set('fos_user.mailer.twig_symfony', TwigSymfonyMailer::class)
        ->private()
        ->args([
            service('mailer'),
            service('router'),
            service('twig'),
            [
                'template' => ['confirmation' => '%fos_user.registration.confirmation.template%', 'resetting' => '%fos_user.resetting.email.template%'],
                'from_email' => ['confirmation' => '%fos_user.registration.confirmation.from_address%', 'resetting' => '%fos_user.resetting.email.from_address%'],
            ],
        ]);

    $services->set('fos_user.mailer.noop', NoopMailer::class)
        ->private();
};

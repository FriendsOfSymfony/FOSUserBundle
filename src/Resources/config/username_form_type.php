<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\Form\DataTransformer\UserToUsernameTransformer;
use FOS\UserBundle\Form\Type\UsernameFormType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.username_form_type', UsernameFormType::class)
        ->args([service('fos_user.user_to_username_transformer')])
        ->tag('form.type', ['alias' => 'fos_user_username']);

    $services->set('fos_user.user_to_username_transformer', UserToUsernameTransformer::class)
        ->private()
        ->args([service('fos_user.user_manager')]);
};

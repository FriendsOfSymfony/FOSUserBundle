<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle;

/**
 * Contains all events thrown in the FOSUserBundle
 */
final class FOSUserEvents
{
    /**
     * The CHANGE_PASSWORD_INITIALIZE event occurs when the change password process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseUserEvent instance.
     */
    const CHANGE_PASSWORD_INITIALIZE = 'fos_user.change_password.edit.initialize';

    /**
     * The CHANGE_PASSWORD_SUCCESS event occurs when the change password form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a FOS\UserBundle\Event\FormEvent instance.
     */
    const CHANGE_PASSWORD_SUCCESS = 'fos_user.change_password.edit.success';

    /**
     * The CHANGE_PASSWORD_COMPLETED event occurs after saving the user in the change password process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterUserResponseEvent instance.
     */
    const CHANGE_PASSWORD_COMPLETED = 'fos_user.change_password.edit.completed';

    /**
     * The GROUP_CREATE_INITIALIZE event occurs when the group creation process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     * The event listener method receives a FOS\UserBundle\Event\GroupEvent instance.
     */
    const GROUP_CREATE_INITIALIZE = 'fos_user.group.create.initialize';

    /**
     * The GROUP_CREATE_SUCCESS event occurs when the group creation form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a FOS\UserBundle\Event\FormEvent instance.
     */
    const GROUP_CREATE_SUCCESS = 'fos_user.group.create.success';

    /**
     * The GROUP_CREATE_COMPLETED event occurs after saving the group in the group creation process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterGroupResponseEvent instance.
     */
    const GROUP_CREATE_COMPLETED = 'fos_user.group.create.completed';

    /**
     * The GROUP_DELETE_COMPLETED event occurs after deleting the group.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterGroupResponseEvent instance.
     */
    const GROUP_DELETE_COMPLETED = 'fos_user.group.delete.completed';

    /**
     * The GROUP_EDIT_INITIALIZE event occurs when the group editing process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseGroupEvent instance.
     */
    const GROUP_EDIT_INITIALIZE = 'fos_user.group.edit.initialize';

    /**
     * The GROUP_EDIT_SUCCESS event occurs when the group edit form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a FOS\UserBundle\Event\FormEvent instance.
     */
    const GROUP_EDIT_SUCCESS = 'fos_user.group.edit.success';

    /**
     * The GROUP_EDIT_COMPLETED event occurs after saving the group in the group edit process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterGroupResponseEvent instance.
     */
    const GROUP_EDIT_COMPLETED = 'fos_user.group.edit.completed';

    /**
     * The PROFILE_EDIT_INITIALIZE event occurs when the profile editing process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseUserEvent instance.
     */
    const PROFILE_EDIT_INITIALIZE = 'fos_user.profile.edit.initialize';

    /**
     * The PROFILE_EDIT_SUCCESS event occurs when the profile edit form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a FOS\UserBundle\Event\FormEvent instance.
     */
    const PROFILE_EDIT_SUCCESS = 'fos_user.profile.edit.success';

    /**
     * The PROFILE_EDIT_COMPLETED event occurs after saving the user in the profile edit process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterUserResponseEvent instance.
     */
    const PROFILE_EDIT_COMPLETED = 'fos_user.profile.edit.completed';

    /**
     * The REGISTRATION_INITIALIZE event occurs when the registration process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     * The event listener method receives a FOS\UserBundle\Event\UserEvent instance.
     */
    const REGISTRATION_INITIALIZE = 'fos_user.registration.initialize';

    /**
     * The REGISTRATION_SUCCESS event occurs when the registration form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a FOS\UserBundle\Event\FormEvent instance.
     */
    const REGISTRATION_SUCCESS = 'fos_user.registration.success';

    /**
     * The REGISTRATION_COMPLETED event occurs after saving the user in the registration process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterUserResponseEvent instance.
     */
    const REGISTRATION_COMPLETED = 'fos_user.registration.completed';

    /**
     * The REGISTRATION_CONFIRM event occurs just before confirming the account.
     *
     * This event allows you to access the user which will be confirmed.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseUserEvent instance.
     */
    const REGISTRATION_CONFIRM = 'fos_user.registration.confirm';

    /**
     * The REGISTRATION_CONFIRMED event occurs after confirming the account.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterUserResponseEvent instance.
     */
    const REGISTRATION_CONFIRMED = 'fos_user.registration.confirmed';

    /**
     * The RESETTING_RESET_INITIALIZE event occurs when the resetting process is initialized.
     *
     * This event allows you to set the response to bypass the processing.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseUserEvent instance.
     */
    const RESETTING_RESET_INITIALIZE = 'fos_user.resetting.reset.initialize';

    /**
     * The RESETTING_RESET_SUCCESS event occurs when the resetting form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a FOS\UserBundle\Event\FormEvent instance.
     */
    const RESETTING_RESET_SUCCESS = 'fos_user.resetting.reset.success';

    /**
     * The RESETTING_RESET_COMPLETED event occurs after saving the user in the resetting process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\FilterUserResponseEvent instance.
     */
    const RESETTING_RESET_COMPLETED = 'fos_user.resetting.reset.completed';

    /**
     * The SECURITY_IMPLICIT_LOGIN event occurs when the user is logged in programmatically.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\UserEvent instance.
     */
    const SECURITY_IMPLICIT_LOGIN = 'fos_user.security.implicit_login';
}

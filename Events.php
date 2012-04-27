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
 * Contains constants used in the FOSUserBundle event dispatcher.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
final class Events
{
    /**
     * The CREATE event occurs when a new user is created. This
     * event will only fire if the UserManager is used to create
     * the user.
     *
     * A UserEvent is passed into the listeners of this event.
     */
    const USER_CREATE = 'fos_user.user.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the User.
     *
     * This event allows you to modify the data in the User or react to
     * changes prior to the object being persisted.
     *
     * A UserPersistEvent is passed into listeners of this event.
     *
     * Persistence can be aborted by calling $event->abortPersist()
     */
    const USER_PRE_PERSIST = 'fos_user.user.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persists the User.
     *
     * This event allows you to perform actions that require the
     * user to be persisted prior to be running.
     *
     * A UserEvent is passed into listeners of this event.
     */
    const USER_POST_PERSIST = 'fos_user.user.post_persist';

    /**
     * The PRE_DELETE event occurs prior to a user being deleted
     * from the persistence backend.
     *
     * A UserPersistEvent is passed into listeners of this event, and
     * allows for the cancellation of the deletion action.
     */
    const USER_PRE_DELETE = 'fos_user.user.pre_delete';

    /**
     * The POST_DELETE event occurs after the user is deleted from
     * the persistence backend.
     *
     * A UserEvent is passed into the listeners of this event.
     *
     * The user object supplied in this event will no longer be
     * managed, and changes made will not be recorded. Care must be
     * taken with the data of this event, as some things may no
     * longer be available.
     */
    const USER_POST_DELETE = 'fos_user.user.post_delete';

    /**
     * The RESET_PASSWORD event occurs after a user has confirmed
     * a password reset of their account.
     *
     * A UserEvent is passed into listeners of this request.
     */
    const USER_RESET_PASSWORD = 'fos_user.user.reset_password';

    /**
     * The RESET_PASSWORD event occurs after a user has changed
     * their account password.
     *
     * A UserEvent is passed into listeners of this request.
     */
    const USER_CHANGE_PASSWORD = 'fos_user.user.change_password';

    /**
     * The CREATE event occurs when a new group is created. This
     * event will only fire if the GroupManager is used to create
     * the user.
     *
     * A GroupEvent is passed into the listeners of this event.
     */
    const GROUP_CREATE = 'fos_user.group.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Group.
     *
     * This event allows you to modify the data in the Group or react to
     * changes prior to the object being persisted.
     *
     * A GroupPersistEvent is passed into listeners of this event.
     *
     * Persistence can be aborted by calling $event->abortPersist()
     */
    const GROUP_PRE_PERSIST = 'fos_user.group.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persists the Group.
     *
     * This event allows you to perform actions that require the
     * group to be persisted prior to be running.
     *
     * A GroupEvent is passed into listeners of this event.
     */
    const GROUP_POST_PERSIST = 'fos_user.group.post_persist';

    /**
     * The PRE_DELETE event occurs prior to a group being deleted
     * from the persistence backend.
     *
     * A GroupPersistEvent is passed into listeners of this event, and
     * allows for the cancellation of the deletion action.
     */
    const GROUP_PRE_DELETE = 'fos_user.group.pre_delete';

    /**
     * The POST_DELETE event occurs after the group is deleted from
     * the persistence backend.
     *
     * A GroupEvent is passed into the listeners of this event.
     *
     * The group object supplied in this event will no longer be
     * managed, and changes made will not be recorded. Care must be
     * taken with the data of this event, as some things may no
     * longer be available.
     */
    const GROUP_POST_DELETE = 'fos_user.group.post_delete';
}
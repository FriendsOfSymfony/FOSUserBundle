<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;

/**
 * This mailer does nothing.
 * It is used when the 'email' configuration is not set,
 * and allows to use this bundle without a mailer component.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
final class NoopMailer implements MailerInterface
{
    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        // nothing happens.
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        // nothing happens.
    }
}

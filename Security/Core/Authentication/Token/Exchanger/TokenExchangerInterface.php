<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security\Core\Authentication\Token\Exchanger;

/**
 *
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 */
interface TokenExchangerInterface
{

    public function getAccessToken($temporaryCode);

}
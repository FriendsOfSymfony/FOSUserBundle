<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\OAuth\Api\Provider;

use Buzz\Client\ClientInterface;

/**
 *
 *
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 */
interface ApiProviderInterface
{

    public function __construct(ClientInterface $client);

    function setAccessToken($token);

    function getUsername();

    function getEmail();

    function getKey();

}
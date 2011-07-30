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

use Buzz\Message\Request;
use Buzz\Message\Response;
use Buzz\Client\ClientInterface;
/**
 *
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 */
class OAuthExchanger implements TokenExchangerInterface
{
    protected $client;
    protected $host;
    protected $endpoint;
    protected $clientId;
    protected $clientSecret;


    public function __construct(ClientInterface $client, $host, $endpoint, $clientId, $clientSecret)
    {
        $this->client           = $client;
        $this->host             = $host;
        $this->endpoint         = $endpoint;
        $this->clientId         = $clientId;
        $this->clientSecret     = $clientSecret;
    }

    public function getAccessToken($temporaryCode)
    {
        $request = new Request(Request::METHOD_POST, $this->endpoint, $this->host);


        $request -> setContent(http_build_query(array(
            'client_id'         => $this->clientId,
            'client_secret'     => $this->clientSecret,
            'code'              => $temporaryCode,
        )));

        $response = new Response();
        $this->client->send($request, $response);
var_dump($response->getContent());
        $hasToken = preg_match('/access_token=(.*)\&?/U', $response->getContent(), $matches);
        if($hasToken) {
            return $matches[1];
        }
        return;
    }


}
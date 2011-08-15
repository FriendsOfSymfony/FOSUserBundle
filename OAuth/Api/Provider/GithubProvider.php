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


use Buzz\Message\Request,
    Buzz\Message\Response,
    Buzz\Client\ClientInterface;

/**
 *
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 */
class GithubProvider implements ApiProviderInterface
{
    protected $client;
    protected $accessToken;

    private $providerData;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }


    public function getUsername()
    {
        $this->getProviderData();
        return $this->providerData->login;
        // @todo: handle exception
    }

    public function getEmail()
    {
        $this->getProviderData();
        return $this->providerData->email;
    }

    protected function getProviderData()
    {
        if(is_null($this->providerData)) {
            $this->fetchProviderData();
        }
    }

    private function fetchProviderData()
    {
        $request = new Request(
            'GET',
            sprintf('/user?access_token=%s', $this->accessToken),
            'https://api.github.com'
        );
        $response = new Response();

        $this->client->send($request, $response);
        $this->providerData = json_decode($response->getContent());
    }

    public function getKey()
    {
        return 'github';
    }

}
<?php

/**
 *
 *
 * Copyright (c) 2011, Marcel Beerta <marcel@etcpasswd.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
 *
 * * Neither the name of Sebastian Bergmann nor the names of his
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP version 5.3
 *
 * @category Etcpasswd
 * @package  Etcpasswd
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  SVN: $$Id$$
 * @link     http://www.etcpasswd.de
 */

namespace FOS\UserBundle\Security\Core\Authentication\Token\Exchanger;

use Buzz\Message\Request;
use Buzz\Message\Response;
use Buzz\Client\ClientInterface;
/**
 *
 *
 * @category Etcpasswd
 * @package  Etcpasswd
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: 0.1.0
 * @link     http://www.etcpasswd.de
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
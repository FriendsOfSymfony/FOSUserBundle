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

namespace FOS\UserBundle\Security\Http\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use FOS\UserBundle\Security\Core\Authentication\Token\OAuthToken;
use FOS\UserBundle\Security\Core\Authentication\Token\Exchanger\TokenExchangerInterface;

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
class OAuthListener implements ListenerInterface
{

    protected $securityContext;
    protected $authenticationManager;
    protected $exchanger;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, TokenExchangerInterface $exchanger)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->exchanger = $exchanger;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $code = $request->get('code');
        if (is_null($code)) {
            $response = new Response();
            // TODO: Fix URL
            $response->headers->set('Location', 'https://github.com/login/oauth/authorize?client_id=68ef21c49fc2b6c5f9f7&scope=public_repo');
            $response->setStatusCode(302);
            $event->setResponse($response);
            return;
        }


        // get the access token
        $token = $this->exchanger->getAccessToken($code);
        var_dump($token);
        exit();
        if (!is_null($token)) {
            $authToken = new OAuthToken();
            $authToken->setUser($token);

            try {
                $returnValue = $this->authenticationManager->authenticate($token);
                if ($returnValue instanceof TokenInterface) {
                    return $this->securityContext->setToken($returnValue);
                } else if ($returnValue instanceof Response) {
                    return $event->setResponse($returnValue);
                }
            } catch (AuthenticationException $e) {
                // you might log something here
            }
        }

        $response = new Response();
        $response->setStatusCode(403);

        $event->setResponse($response);
    }

}
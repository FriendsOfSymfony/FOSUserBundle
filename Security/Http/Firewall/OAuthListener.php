<?php


/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
 * @author   Marcel Beerta <marcel@etcpasswd.de>
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
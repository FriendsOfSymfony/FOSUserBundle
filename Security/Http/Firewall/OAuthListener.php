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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FOS\UserBundle\Security\Core\Authentication\Token\OAuthToken;
use FOS\UserBundle\Security\Core\Authentication\Token\Exchanger\TokenExchangerInterface;
use FOS\UserBundle\OAuth\Api\Provider\ApiProviderInterface;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 *
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 *
 * @todo this needs some cleanup work, way too many dependencies now
 */
class OAuthListener implements ListenerInterface
{

    protected $securityContext;
    protected $authenticationManager;
    protected $exchanger;
    protected $apiProvider;
    protected $authenticationUrl;
    protected $userManager;
    protected $encoderFactory;
    protected $algorithm;
    protected $clientId;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, TokenExchangerInterface $exchanger, ApiProviderInterface $provider, UserManagerInterface $userManager, EncoderFactoryInterface $encoderFactory, $algorithm, $authenticationUrl, $clientId)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->exchanger = $exchanger;
        $this->apiProvider = $provider;
        $this->authenticationUrl = $authenticationUrl;
        $this->userManager = $userManager;
        $this->encoderFactory = $encoderFactory;
        $this->algorithm = $algorithm;
        $this->clientId  = $clientId;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $code = $request->get('code');

        // No "code" means we haven't been redirecting the user to the
        // authentication page yet
        if (is_null($code)) {
            $response = new Response();
            $response->headers->set('Location', sprintf('%s?client_id=%s', $this->authenticationUrl, $this->clientId));
            $response->setStatusCode(302);
            $event->setResponse($response);
            return;
        }

        // get the access token
        $token = $this->exchanger->getAccessToken($code);
        if (!is_null($token)) {
            $this->apiProvider->setAccessToken($token);
            $username = $this->apiProvider->getUsername();
            $email    = $this->apiProvider->getEmail();

            // Now comes the trick, we lookup the user, if he's not here,
            // we just create him. Otherwise we validate the "password"
            // which in this case is the accessToken.
            //
            // @todo: figure out what to do when the token changes
            $user = $this->userManager->findUserByUsername($username);

            if (is_null($user)) {
                $user = $this->userManager->createUser();
                $user -> setUsername($username);
                $user -> setPlainPassword($token);
                $user -> setEmail($email);
                $user -> setEnabled(true);
                $user -> setConfirmationToken(null);
                $this->userManager->updateUser($user);
            }

            $authenticationToken = new OAuthToken($user->getRoles());
            $authenticationToken -> setUser($user);
            $authenticationToken -> setAttribute('access_token', $token);

            try {
                $returnValue = $this->authenticationManager->authenticate($authenticationToken);
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
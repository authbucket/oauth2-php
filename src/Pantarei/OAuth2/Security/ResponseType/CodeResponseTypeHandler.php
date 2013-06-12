<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\ResponseType;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Code response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseTypeHandler extends AbstractTokenResponseTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        GetResponseEvent $event,
        TokenTypeHandlerInterface $tokenTypeHandler,
        array $modelManagers,
        $providerKey
    )
    {
        $request = $event->getRequest();

        $query = array(
            'client_id' => $request->query->get('client_id'),
            'redirect_uri' => $request->query->get('redirect_uri'),
            'scope' => $request->query->get('scope'),
            'state' => $request->query->get('state'),
        );
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidScopeException();
        }

        // Set client_id from GET.
        $client_id = $request->query->get('client_id');

        // Check and set redirect_uri.
        $redirect_uri = $this->checkRedirectUri($request, $modelManagers, $client_id);

        // Set username from token.
        $username = $securityContext->getToken()->getUsername();
        
        // Check and set scope.
        $scope = $this->checkScope($request, $modelManagers);

        // Check and set state.
        $state = $this->checkState($request);

        // Generate code, store to backend and set token response.
        $parameters = $this->createCode(
            $modelManagers,
            $client_id,
            $redirect_uri,
            $username,
            $scope,
            $state
        );
        $this->setResponse($event, $parameters);
    }

    private function createCode(
        array $modelManagers,
        $client_id,
        $redirect_uri,
        $username = '',
        $scope = array(),
        $state = null
    )
    {
        $code = $modelManagers['code']->createCode();
        $code->setCode(md5(uniqid(null, true)))
            ->setClientId($client_id)
            ->setRedirectUri($redirect_uri)
            ->setUsername($username)
            ->setExpires(time() + 600)
            ->setScope($scope);
        $modelManagers['code']->upateCode($code);

        $parameters = array(
            'code' => $code->getCode(),
            'state' => $state,
        );

        return $parameters;
    }
}

<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\GrantType;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Authorization code grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantTypeHandler implements GrantTypeHandlerInterface
{
    public function handle(
        AuthenticationManagerInterface $authenticationManager,
        GetResponseEvent $event,
        $tokenTypeHandler,
        array $modelManagers,
        $client_id,
        $providerKey
    )
    {
        $request = $event->getRequest();

        $query = array(
            'code' => $request->request->get('code'),
        );
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        $this->checkRedirectUri($request, $modelManagers, $client_id);

        $code = $request->request->get('code');

        // Check code with database record.
        $result = $modelManagers['code']->findCodeByCode($code);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < time()) {
            throw new InvalidGrantException();
        }

        $username = $result->getUsername();
        $scope = $result->getScope();

        // Generate access_token, store to backend and set token response.
        $tokenTypeHandler->setResponse(
            $event,
            $modelManagers,
            $client_id,
            $username,
            $scope
        );
    }

    private function checkRedirectUri(Request $request, array $modelManagers, $client_id)
    {
        $redirect_uri = $request->request->get('redirect_uri');

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $result = $modelManagers['client']->findClientByClientId($client_id);
        if ($result !== null && $result->getRedirectUri()) {
            $stored = $result->getRedirectUri();
        }

        // At least one of: existing redirect URI or input redirect URI must be
        // specified.
        if (!$stored && !$redirect_uri) {
            throw new InvalidRequestException();
        }

        // If there's an existing uri and one from input, verify that they match.
        if ($stored && $redirect_uri) {
            // Ensure that the input uri starts with the stored uri.
            if (strcasecmp(substr($redirect_uri, 0, strlen($stored)), $stored) !== 0) {
                throw new InvalidRequestException();
            }
        }
    }
}

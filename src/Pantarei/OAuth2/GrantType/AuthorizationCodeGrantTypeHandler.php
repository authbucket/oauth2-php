<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\GrantType;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\Filter;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Authorization code grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        $providerKey
    )
    {
        // Check and set client_id.
        $client_id = $this->checkClientId($request, $modelManagerFactory);

        // Fetch username and scope from stored code.
        list($username, $scope) = $this->checkCode($request, $modelManagerFactory, $client_id);

        // Check and set redirect_uri.
        $redirect_uri = $this->checkRedirectUri($request, $modelManagerFactory, $client_id);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope
        );
        return $this->setResponse($parameters);
    }

    private function checkCode(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id
    )
    {
        $code = $request->request->get('code');

        // code is required and must in valid format.
        $query = array(
            'code' => $code,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        // Check code with database record.
        $codeManager = $modelManagerFactory->getModelManager('code');
        $result = $codeManager->findCodeByCode($code);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < time()) {
            throw new InvalidGrantException();
        }

        return array($result->getUsername(), $result->getScope());
    }

    private function checkRedirectUri(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id
    )
    {
        $redirect_uri = $request->request->get('redirect_uri');

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $clientManager = $modelManagerFactory->getModelManager('client');
        $result = $clientManager->findClientByClientId($client_id);
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

        return $redirect_uri ? $redirect_uri : $stored;
    }
}

<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use AuthBucket\OAuth2\Exception\InvalidGrantException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\Filter;
use AuthBucket\OAuth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Authorization code grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    )
    {
        // Fetch client_id from authenticated token.
        $clientId = $this->checkClientId($securityContext);

        // Fetch username and scope from stored code.
        list($username, $scope) = $this->checkCode($request, $modelManagerFactory, $clientId);

        // Check and set redirect_uri.
        $redirectUri = $this->checkRedirectUri($request, $modelManagerFactory, $clientId);

        // Check state from stored code.
        $this->checkState($request, $modelManagerFactory);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $clientId,
            $username,
            $scope
        );

        return JsonResponse::create($parameters);
    }

    /**
     * Fetch code from POST.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     * @param string                       $clientId            Corresponding client_id that code should belongs to.
     *
     * @return array A list with stored username and scope, originally grant in authorize endpoint.
     *
     * @throw InvalidRequestException If code in invalid format.
     * @throw InvalidGrantException If code provided is no longer valid.
     */
    private function checkCode(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId
    )
    {
        $code = $request->request->get('code');

        // code is required and must in valid format.
        if (!Filter::filter(array('code' => $code))) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Check code with database record.
        $codeManager = $modelManagerFactory->getModelManager('code');
        $result = $codeManager->readModelOneBy(array(
            'code' => $code,
        ));
        if ($result === null || $result->getClientId() !== $clientId) {
            throw new InvalidGrantException(array(
                'error_description' => 'The provided authorization grant is invalid.',
            ));
        } elseif ($result->getExpires() < new \DateTime()) {
            throw new InvalidGrantException(array(
                'error_description' => 'The provided authorization grant is expired.',
            ));
        }

        return array($result->getUsername(), $result->getScope());
    }

    /**
     * Fetch redirect_uri from POST, or stored record.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     * @param string                       $clientId            Corresponding client_id that code should belongs to.
     *
     * @return string The supplied redirect_uri from incoming request, or from stored record.
     *
     * @throw InvalidRequestException If redirect_uri not exists in both incoming request and database record, or supplied value not match with stord record.
     */
    private function checkRedirectUri(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId
    )
    {
        $redirectUri = $request->request->get('redirect_uri');

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $clientManager = $modelManagerFactory->getModelManager('client');
        $result = $clientManager->readModelOneBy(array(
            'clientId' => $clientId,
        ));
        if ($result !== null && $result->getRedirectUri()) {
            $stored = $result->getRedirectUri();
        }

        // At least one of: existing redirect URI or input redirect URI must be
        // specified.
        if (!$stored && !$redirectUri) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request is missing a required parameter.',
            ));
        }

        // If there's an existing uri and one from input, verify that they match.
        if ($stored && $redirectUri) {
            // Ensure that the input uri starts with the stored uri.
            if (strcasecmp(substr($redirectUri, 0, strlen($stored)), $stored) !== 0) {
                throw new InvalidRequestException(array(
                    'error_description' => 'The provided authorization grant does not match the redirection URI used in the authorization request.',
                ));
            }
        }

        return $redirectUri ?: $stored;
    }

    /**
     * Check state from POST.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     *
     * @throw InvalidRequestException If supplied state value not match with stored record.
     */
    private function checkState(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $state = $request->request->get('state');
        $code = $request->request->get('code');

        // state is required and in valid format.
        if (!Filter::filter(array('state' => $state))) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Check state with database record.
        $codeManager = $modelManagerFactory->getModelManager('code');
        $result = $codeManager->readModelOneBy(array(
            'code' => $code,
        ));
        if ($result === null || $result->getState() !== $state) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }
    }
}

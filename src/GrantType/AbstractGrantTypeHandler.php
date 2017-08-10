<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use AuthBucket\OAuth2\Exception\InvalidGrantException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\AuthorizeInterface;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Symfony\Component\Security\Core\Authentication\Token\ClientCredentialsToken;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Shared grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractGrantTypeHandler implements GrantTypeHandlerInterface
{
    const GRANT_TYPE = null;

    protected $tokenStorage;
    protected $encoderFactory;
    protected $validator;
    protected $modelManagerFactory;
    protected $tokenTypeHandlerFactory;
    protected $userProvider;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EncoderFactoryInterface $encoderFactory,
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoderFactory;
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
        $this->userProvider = $userProvider;
    }

    /**
     * Fetch client_id from authenticated token.
     *
     * @return string Supplied client_id from authenticated token
     *
     * @throw ServerErrorException If supplied token is not a ClientCredentialsToken instance.
     */
    protected function checkClientId()
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token instanceof ClientCredentialsToken) {
            throw new ServerErrorException([
                'error_description' => 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.',
            ]);
        }

        return $token->getClientId();
    }

    /**
     * Fetch scope from POST.
     *
     * @param Request $request Incoming request object
     *
     * @return array|null Supplied scope in array from incoming request, or null if none given
     *
     * @throw InvalidRequestException If supplied scope in bad format.
     * @throw InvalidScopeException If supplied scope outside supported scope range.
     */
    protected function checkScope(
        Request $request,
        $clientId,
        $username
    ) {
        // scope may not exists.
        $scope = $request->request->get('scope');
        if (empty($scope)) {
            return;
        }

        // scope must be in valid format.
        $errors = $this->validator->validate($scope, [
            new \AuthBucket\OAuth2\Symfony\Component\Validator\Constraints\Scope(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $scope = preg_split('/\s+/', $scope);

        // Compare if given scope within all supported scopes.
        $scopeSupported = [];
        $scopeManager = $this->modelManagerFactory->getModelManager('scope');
        $result = $scopeManager->readModelAll();
        if ($result !== null) {
            foreach ($result as $row) {
                $scopeSupported[] = $row->getScope();
            }
        }
        if (array_intersect($scope, $scopeSupported) !== $scope) {
            throw new InvalidScopeException([
                'error_description' => 'The requested scope is unknown.',
            ]);
        }

        // Compare if given scope within all authorized scopes.
        $scopeAuthorized = [];
        $grantTypeAuthorized = [];
        $authorizeManager = $this->modelManagerFactory->getModelManager('authorize');
        /** @var AuthorizeInterface $result */
        $result = $authorizeManager->readModelOneBy([
            'clientId' => $clientId,
            'username' => $username,
        ]);
        if ($result !== null) {
            $scopeAuthorized = $result->getScope();
            $grantTypeAuthorized = $result->getGrantType();
        }
        if (array_intersect($scope, $scopeAuthorized) !== $scope) {
            throw new InvalidScopeException([
                'error_description' => 'The requested scope exceeds the scope granted by the resource owner.',
            ]);
        }
        if (!in_array(static::GRANT_TYPE, $grantTypeAuthorized)) {
            throw new InvalidGrantException([
                'error_description' => 'The requested grant is invalid.',
            ]);
        }

        return $scope;
    }
}

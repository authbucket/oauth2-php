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

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\ClientToken;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Shared grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractGrantTypeHandler implements GrantTypeHandlerInterface
{
    protected $securityContext;
    protected $userChecker;
    protected $encoderFactory;
    protected $validator;
    protected $modelManagerFactory;
    protected $tokenTypeHandlerFactory;
    protected $userProvider;

    public function __construct(
        SecurityContextInterface $securityContext,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory,
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    )
    {
        $this->securityContext = $securityContext;
        $this->userChecker = $userChecker;
        $this->encoderFactory = $encoderFactory;
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
        $this->userProvider = $userProvider;
    }

    /**
     * Fetch client_id from authenticated token.
     *
     * @return string Supplied client_id from authenticated token.
     *
     * @throw ServerErrorException If supplied token is not a ClientToken instance.
     */
    protected function checkClientId()
    {
        $clientId = $this->securityContext->getToken()->getClientId();

        return $clientId;
    }

    /**
     * Fetch scope from POST.
     *
     * @param Request $request Incoming request object.
     *
     * @return array|null Supplied scope in array from incoming request, or null if none given.
     *
     * @throw InvalidRequestException If supplied scope in bad format.
     * @throw InvalidScopeException If supplied scope outside supported scope range.
     */
    protected function checkScope(
        Request $request,
        $clientId,
        $username
    )
    {
        $scope = $request->request->get('scope', null);

        // scope may not exists.
        if ($scope) {
            // scope must be in valid format.
            if (!Filter::filter(array('scope' => $scope))) {
                throw new InvalidRequestException(array(
                    'error_description' => 'The request includes an invalid parameter value.',
                ));
            }

            $scope = preg_split('/\s+/', $scope);

            // Compare if given scope within all supported scopes.
            $scopeSupported = array();
            $scopeManager = $this->modelManagerFactory->getModelManager('scope');
            $result = $scopeManager->readModelAll();
            if ($result !== null) {
                foreach ($result as $row) {
                    $scopeSupported[] = $row->getScope();
                }
            }
            if (array_intersect($scope, $scopeSupported) !== $scope) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope is unknown.',
                ));
            }

            // Compare if given scope within all authorized scopes.
            $scopeAuthorized = array();
            $authorizeManager = $this->modelManagerFactory->getModelManager('authorize');
            $result = $authorizeManager->readModelOneBy(array(
                'clientId' => $clientId,
                'username' => $username,
            ));
            if ($result !== null) {
                $scopeAuthorized = $result->getScope();
            }
            if (array_intersect($scope, $scopeAuthorized) !== $scope) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope exceeds the scope granted by the resource owner.',
                ));
            }
        }

        return $scope;
    }
}

<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResponseType;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\RedirectResponse;
use AuthBucket\OAuth2\Validator\Constraints\ClientId;
use AuthBucket\OAuth2\Validator\Constraints\RedirectUri;
use AuthBucket\OAuth2\Validator\Constraints\ResponseType;
use AuthBucket\OAuth2\Validator\Constraints\Scope;
use AuthBucket\OAuth2\Validator\Constraints\State;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Code response type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseTypeHandler extends AbstractResponseTypeHandler
{
    public function handle(
        Request $request,
        SecurityContextInterface $securityContext,
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        // Validate input parameters.
        $parameters = array(
            'client_id' => $request->query->get('client_id'),
            'redirect_uri' => $request->query->get('redirect_uri'),
            'scope' => $request->query->get('scope'),
            'state' => $request->query->get('state'),
            'username' => $securityContext->getToken()->getUsername(),
        );

        $constraints = new Collection(array(
            'client_id' => array(new NotBlank(), new ClientId()),
            'redirect_uri' => new RedirectUri(),
            'scope' => new Scope(),
            'state' => array(new NotBlank(), new State()),
            'username' => array(new NotBlank(), new Username()),
        ));

        $errors = $validator->validateValue($parameters, $constraints);
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Fetch username from authenticated token.
        $username = $this->checkUsername($securityContext, $validator);

        // Fetch and check client_id.
        $clientId = $this->checkClientId($request, $validator, $modelManagerFactory);

        // Fetch and check redirect_uri.
        $redirectUri = $this->checkRedirectUri($request, $validator, $modelManagerFactory, $clientId);

        // Fetch and check state.
        $state = $this->checkState($request, $validator, $redirectUri);

        // Fetch and check scope.
        $scope = $this->checkScope(
            $request,
            $validator,
            $modelManagerFactory,
            $clientId,
            $username,
            $redirectUri,
            $state
        );

        // Generate parameters, store to backend and set response.
        $codeManager =  $modelManagerFactory->getModelManager('code');
        $code = $codeManager->createModel(array(
            'code' => md5(uniqid(null, true)),
            'clientId' => $clientId,
            'username' => $username,
            'redirectUri' => $redirectUri,
            'expires' => new \DateTime('+10 minutes'),
            'scope' => $scope,
        ));

        $parameters = array(
            'code' => $code->getCode(),
            'state' => $state,
        );

        return RedirectResponse::create($redirectUri, $parameters);
    }
}

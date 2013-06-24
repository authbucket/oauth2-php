<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\ResponseType;

use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Code response type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseTypeHandler extends AbstractResponseTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        // Fetch username from authenticated token.
        $username = $this->checkUsername($securityContext);

        // Set client_id from GET.
        $client_id = $this->checkClientId($request, $modelManagerFactory);

        // Check and set redirect_uri.
        $redirect_uri = $this->checkRedirectUri($request, $modelManagerFactory, $client_id);

        // Check and set scope.
        $scope = $this->checkScope($request, $modelManagerFactory, $client_id, $username);

        // Check and set state.
        $state = $this->checkState($request);

        // Generate parameters, store to backend and set response.
        $modelManager =  $modelManagerFactory->getModelManager('code');

        $code = $modelManager->createCode();
        $code->setCode(md5(uniqid(null, true)))
            ->setClientId($client_id)
            ->setRedirectUri($redirect_uri)
            ->setUsername($username)
            ->setExpires(time() + 600)
            ->setScope($scope);
        $modelManager->updateCode($code);

        $parameters = array(
            'code' => $code->getCode(),
            'state' => $state,
        );

        return $this->setResponse($redirect_uri, $parameters);
    }
}

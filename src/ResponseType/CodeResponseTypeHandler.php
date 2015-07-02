<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResponseType;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Code response type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseTypeHandler extends AbstractResponseTypeHandler
{
    public function handle(Request $request)
    {
        // Fetch username from authenticated token.
        $username = $this->checkUsername();

        // Fetch and check client_id.
        $clientId = $this->checkClientId($request);

        // Fetch and check redirect_uri.
        $redirectUri = $this->checkRedirectUri($request, $clientId);

        // Fetch and check state.
        $state = $this->checkState($request, $redirectUri);

        // Fetch and check scope.
        $scope = $this->checkScope(
            $request,
            $clientId,
            $username,
            $redirectUri,
            $state
        );

        // Generate parameters, store to backend and set response.
        $codeManager =  $this->modelManagerFactory->getModelManager('code');
        $class = $codeManager->getClassName();
        $code = new $class();
        $code->setCode(md5(uniqid(null, true)))
            ->setClientId($clientId)
            ->setUsername($username)
            ->setRedirectUri($redirectUri)
            ->setExpires(new \DateTime('+10 minutes'))
            ->setScope((array) $scope);
        $code = $codeManager->createModel($code);

        $parameters = array(
            'code' => $code->getCode(),
            'state' => $state,
        );

        $redirectUri = Request::create($redirectUri, 'GET', $parameters)->getUri();

        return RedirectResponse::create($redirectUri);
    }
}

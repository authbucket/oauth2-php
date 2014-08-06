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

use AuthBucket\OAuth2\Util\RedirectResponse;
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
        $code = $codeManager->createModel(array(
            'code' => md5(uniqid(null, true)),
            'state' => $state,
            'clientId' => $clientId,
            'username' => $username,
            'redirectUri' => $redirectUri,
            'expires' => new \DateTime('+10 minutes'),
            'scope' => (array) $scope,
        ));

        $parameters = array(
            'code' => $code->getCode(),
            'state' => $state,
        );

        return RedirectResponse::create($redirectUri, $parameters);
    }
}

<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\Endpoint;

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Util\ParameterUtils;
use Symfony\Component\HttpFoundation\Request;

class AuthorizeEndpointHandler implements EndpointHandlerInterface
{
    public function handle(Request $request)
    {
        // Fetch response_type from GET.
        $response_type = $this->getResponseType($request);

        // Handle authorize endpoint response.
        return $this->responseTypeHandlerFactory->getResponseTypeHandler($response_type)->handle(
            $this->securityContext,
            $this->authenticationManager,
            $request,
            $this->modelManagerFactory,
            $this->tokenTypeHandlerFactory,
            $this->providerKey
        );
    }

    private function getResponseType(Request $request)
    {
        // response_type should NEVER come from POST.
        if ($request->request->get('response_type')) {
            throw new InvalidRequestException();
        }

        // Validate and set response_type.
        $response_type = $request->request->get('response_type');
        $query = array(
            'response_type' => $response_type
        );
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        return $response_type;
    }
}

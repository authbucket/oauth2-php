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

class TokenEndpointHandler extends AbstractEndpointHandler
{
    public function handle(Request $request)
    {
        // Fetch grant_type from POST.
        $grant_type = $this->getGrantType($request);

        // Handle token endpoint response.
        return $this->grantTypeHandlerFactory->getGrantTypeHandler($grant_type)->handle(
            $this->securityContext,
            $this->authenticationManager,
            $request,
            $this->modelManagerFactory,
            $this->tokenTypeHandlerFactory,
            $this->providerKey
        );
    }

    private function getGrantType(Request $request)
    {
        // grant_type should NEVER come from GET.
        if ($request->query->get('grant_type')) {
            throw new InvalidRequestException();
        }

        // grant_type must set and in valid format.
        $grant_type = $request->request->get('grant_type');
        $query = array(
            'grant_type' => $grant_type
        );
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        return $grant_type;
    }
}

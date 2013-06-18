<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Controller;

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Util\Filter;
use Symfony\Component\HttpFoundation\Request;

/**
 * OAuth2 token endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenController extends AbstractController
{
    public function handle(Request $request)
    {
        // Fetch grant_type from POST.
        $grant_type = $this->getGrantType($request);

        // Handle token endpoint response.
        return $this->grantTypeHandlerFactory->getGrantTypeHandler($grant_type)->handle(
            $this->securityContext,
            $request,
            $this->modelManagerFactory,
            $this->tokenTypeHandlerFactory
        );
    }

    private function getGrantType(Request $request)
    {
        // grant_type must set and in valid format.
        $grant_type = $request->request->get('grant_type');
        $query = array(
            'grant_type' => $grant_type
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        return $grant_type;
    }
}

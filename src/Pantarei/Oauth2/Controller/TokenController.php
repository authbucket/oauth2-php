<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Controller;

use Pantarei\Oauth2\Exception\InvalidRequestException;
use Pantarei\Oauth2\Exception\UnsupportedGrantTypeException;
use Pantarei\Oauth2\GrantType\GrantTypeHandlerFactoryInterface;
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\Oauth2\Util\Filter;
use Pantarei\Oauth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Oauth2 token endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenController
{
    protected $securityContext;
    protected $modelManagerFactory;
    protected $grantTypeHandlerFactory;
    protected $tokenTypeHandlerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        ModelManagerFactoryInterface $modelManagerFactory,
        GrantTypeHandlerFactoryInterface $grantTypeHandlerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        $this->securityContext = $securityContext;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->grantTypeHandlerFactory = $grantTypeHandlerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
    }

    public function tokenAction(Request $request)
    {
        try {
            // Fetch grant_type from POST.
            $grant_type = $this->getGrantType($request);

            // Handle token endpoint response.
            return $this->grantTypeHandlerFactory->getGrantTypeHandler($grant_type)->handle(
                $this->securityContext,
                $request,
                $this->modelManagerFactory,
                $this->tokenTypeHandlerFactory
            );
        } catch (InvalidRequestException $e) {
            return JsonResponse::create(array('error' => 'invalid_request'), 400);
        } catch (UnsupportedGrantTypeException $e) {
            return JsonResponse::create(array('error' => 'unsupported_grant_type'), 400);
        }
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

<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * OAuth2 resource endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceController
{
    protected $securityContext;

    public function __construct(
        SecurityContextInterface $securityContext
    )
    {
        $this->securityContext = $securityContext;
    }

    public function usernameAction(Request $request)
    {
        $data = array(
            'username' => $this->securityContext->getToken()->getUser(),
        );
        return JsonResponse::create($data);
    }
}

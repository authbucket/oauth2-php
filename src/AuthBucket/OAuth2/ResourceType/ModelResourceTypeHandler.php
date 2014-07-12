<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResourceType;

use AuthBucket\OAuth2\Exception\AccessDeniedException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Model response type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ModelResourceTypeHandler extends AbstractResourceTypeHandler
{
    public function handle(
        HttpKernelInterface $httpKernel,
        ModelManagerFactoryInterface $modelManagerFactory,
        $accessToken,
        array $options = array()
    )
    {
        $accessTokenManager = $modelManagerFactory->getModelManager('access_token');
        $stored = $accessTokenManager->findAccessTokenByAccessToken($accessToken);
        if ($stored === null) {
            throw new AccessDeniedException();
        } elseif ($stored->getExpires() < new \DateTime()) {
            throw new AccessDeniedException();
        }

        return $stored;
    }
}

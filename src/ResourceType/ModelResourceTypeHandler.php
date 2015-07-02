<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResourceType;

use AuthBucket\OAuth2\Exception\InvalidRequestException;

/**
 * Model response type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ModelResourceTypeHandler extends AbstractResourceTypeHandler
{
    public function handle(
        $accessToken,
        array $options = array()
    ) {
        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');
        $accessTokenStored = $accessTokenManager->readModelOneBy(array(
            'accessToken' => $accessToken,
        ));
        if ($accessTokenStored === null) {
            throw new InvalidRequestException(array(
                'error_description' => 'The provided access token is invalid.',
            ));
        } elseif ($accessTokenStored->getExpires() < new \DateTime()) {
            throw new InvalidRequestException(array(
                'error_description' => 'The provided access token is expired.',
            ));
        }

        return $accessTokenStored;
    }
}

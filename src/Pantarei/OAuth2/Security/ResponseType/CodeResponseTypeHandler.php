<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\ResponseType;

use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerInterface;

/**
 * Code response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseTypeHandler extends AbstractResponseTypeHandler
{
    private function generateParameters(
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        $client_id,
        $redirect_uri,
        $username = '',
        $scope = array(),
        $state = null
    )
    {
        $codeManager =  $modelManagerFactory->getModelManager('code');

        $code = $codeManager->createCode();
        $code->setCode(md5(uniqid(null, true)))
            ->setClientId($client_id)
            ->setRedirectUri($redirect_uri)
            ->setUsername($username)
            ->setExpires(time() + 600)
            ->setScope($scope);
        $codeManager->upateCode($code);

        $parameters = array(
            'code' => $code->getCode(),
            'state' => $state,
        );

        return $parameters;
    }
}

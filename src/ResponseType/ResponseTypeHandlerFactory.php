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

use AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * OAuth2 response type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResponseTypeHandlerFactory implements ResponseTypeHandlerFactoryInterface
{
    protected $securityContext;
    protected $validator;
    protected $modelManagerFactory;
    protected $tokenTypeHandlerFactory;
    protected $classes;

    public function __construct(
        SecurityContextInterface $securityContext,
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        array $classes = array()
    ) {
        $this->securityContext = $securityContext;
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;

        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new UnsupportedResponseTypeException(array(
                    'error_description' => 'The authorization server does not support obtaining an authorization code using this method.',
                ));
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('AuthBucket\\OAuth2\\ResponseType\\ResponseTypeHandlerInterface')) {
                throw new UnsupportedResponseTypeException(array(
                    'error_description' => 'The authorization server does not support obtaining an authorization code using this method.',
                ));
            }
        }

        $this->classes = $classes;
    }

    public function getResponseTypeHandler($type = null)
    {
        $type = $type ?: current(array_keys($this->classes));

        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedResponseTypeException(array(
                'error_description' => 'The authorization server does not support obtaining an authorization code using this method.',
            ));
        }

        $class = $this->classes[$type];

        return new $class(
            $this->securityContext,
            $this->validator,
            $this->modelManagerFactory,
            $this->tokenTypeHandlerFactory
        );
    }

    public function getResponseTypeHandlers()
    {
        return $this->classes;
    }
}

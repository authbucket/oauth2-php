<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * OAuth2 grant type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class GrantTypeHandlerFactory implements GrantTypeHandlerFactoryInterface
{
    protected $validator;
    protected $modelManagerFactory;
    protected $tokenTypeHandlerFactory;
    protected $classes;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        array $classes = []
    ) {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;

        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new UnsupportedGrantTypeException([
                    'error_description' => 'The authorization grant type is not supported by the authorization server.',
                ]);
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('AuthBucket\\OAuth2\\GrantType\\GrantTypeHandlerInterface')) {
                throw new UnsupportedGrantTypeException([
                    'error_description' => 'The authorization grant type is not supported by the authorization server.',
                ]);
            }
        }

        $this->classes = $classes;
    }

    public function getGrantTypeHandler($type = null)
    {
        $type = $type ?: current(array_keys($this->classes));

        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedGrantTypeException([
                'error_description' => 'The authorization grant type is not supported by the authorization server.',
            ]);
        }

        $class = $this->classes[$type];

        return new $class(
            $this->validator,
            $this->modelManagerFactory,
            $this->tokenTypeHandlerFactory
        );
    }

    public function getGrantTypeHandlers()
    {
        return $this->classes;
    }
}

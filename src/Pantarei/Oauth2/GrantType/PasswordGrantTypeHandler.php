<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\GrantType;

use Pantarei\Oauth2\Exception\InvalidClientException;
use Pantarei\Oauth2\Exception\InvalidGrantException;
use Pantarei\Oauth2\Exception\InvalidRequestException;
use Pantarei\Oauth2\Exception\InvalidScopeException;
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\Oauth2\Util\Filter;
use Pantarei\Oauth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Password grant type implementation.
 *
 * Note this is a Symfony based specific implementation, 3rd party
 * integration should override this with its own logic.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeHandler extends AbstractGrantTypeHandler
{
    protected $userProvider;

    protected $userChecker;

    protected $encoderFactory;

    public function __construct(
        UserProviderInterface $userProvider,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory
    )
    {
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->encoderFactory = $encoderFactory;
    }

    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        try {
            // Fetch client_id from authenticated token.
            $client_id = $this->checkClientId($securityContext);

            // Check resource owner credentials
            $username = $this->checkUsername($request, $modelManagerFactory);

            // Check and set scope.
            $scope = $this->checkScope($request, $modelManagerFactory, $client_id, $username);
        } catch (InvalidClientException $e) {
            return JsonResponse::create(array('error' => 'invalid_client'), 401);
        } catch (InvalidGrantException $e) {
            return JsonResponse::create(array('error' => 'invalid_grant'), 400);
        } catch (InvalidRequestException $e) {
            return JsonResponse::create(array('error' => 'invalid_request'), 400);
        } catch (InvalidScopeException $e) {
            return JsonResponse::create(array('error' => 'invalid_scope'), 400);
        }

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope
        );
        return JsonResponse::create($parameters);
    }

    /**
     * Fetch username from POST.
     *
     * @param Request $request
     *   Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *   Model manager factory for compare with database record.
     *
     * @return string
     *   The supplied username.
     *
     * @throw InvalidRequestException
     *   If username or password in invalid format.
     * @throw InvalidGrantException
     *   If reported as bad credentials from authentication provider.
     */
    private function checkUsername(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // username and password must exist and in valid format.
        $query = array(
            'username' => $username,
            'password' => $password,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        // Validate credentials with authentication manager.
        try {
            $token = new UsernamePasswordToken($username, $password, 'oauth2');
            $authenticationProvider = new DaoAuthenticationProvider(
                $this->userProvider,
                $this->userChecker,
                'oauth2',
                $this->encoderFactory
            );
            $authenticationProvider->authenticate($token);
        } catch (BadCredentialsException $e) {
            throw new InvalidGrantException();
        }

        return $username;
    }
}

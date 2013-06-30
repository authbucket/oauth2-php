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
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\Oauth2\Util\Filter;
use Pantarei\Oauth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Authorization code grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantTypeHandler extends AbstractGrantTypeHandler
{
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

            // Fetch username and scope from stored code.
            list($username, $scope) = $this->checkCode($request, $modelManagerFactory, $client_id);

            // Check and set redirect_uri.
            $redirect_uri = $this->checkRedirectUri($request, $modelManagerFactory, $client_id);
        } catch (InvalidClientException $e) {
            return JsonResponse::create(array('error' => 'invalid_client'), 401);
        } catch (InvalidGrantException $e) {
            return JsonResponse::create(array('error' => 'invalid_grant'), 400);
        } catch (InvalidRequestException $e) {
            return JsonResponse::create(array('error' => 'invalid_request'), 400);
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
     * Fetch code from POST.
     *
     * @param Request $request
     *   Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *   Model manager factory for compare with database record.
     * @param string client_id
     *   Corresponding client_id that code should belongs to.
     *
     * @return array
     *   A list with stored username and scope, originally grant in authorize
     *   endpoint.
     *
     * @throw InvalidRequestException
     *   If code in invalid format.
     * @throw InvalidGrantException
     *   If code provided is no longer valid.
     */
    private function checkCode(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id
    )
    {
        $code = $request->request->get('code');

        // code is required and must in valid format.
        $query = array(
            'code' => $code,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        // Check code with database record.
        $codeManager = $modelManagerFactory->getModelManager('code');
        $result = $codeManager->findCodeByCode($code);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < new \DateTime()) {
            throw new InvalidGrantException();
        }

        return array($result->getUsername(), $result->getScope());
    }

    /**
     * Fetch redirect_uri from POST, or stored record.
     *
     * @param Request $request
     *   Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *   Model manager factory for compare with database record.
     * @param string client_id
     *   Corresponding client_id that code should belongs to.
     *
     * @return string
     *   The supplied redirect_uri from incoming request, or from stored
     *   record.
     *
     * @throw InvalidRequestException
     *   If redirect_uri not exists in both incoming request and database
     *   record, or supplied value not match with stord record.
     */
    private function checkRedirectUri(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id
    )
    {
        $redirect_uri = $request->request->get('redirect_uri');

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $clientManager = $modelManagerFactory->getModelManager('client');
        $result = $clientManager->findClientByClientId($client_id);
        if ($result !== null && $result->getRedirectUri()) {
            $stored = $result->getRedirectUri();
        }

        // At least one of: existing redirect URI or input redirect URI must be
        // specified.
        if (!$stored && !$redirect_uri) {
            throw new InvalidRequestException();
        }

        // If there's an existing uri and one from input, verify that they match.
        if ($stored && $redirect_uri) {
            // Ensure that the input uri starts with the stored uri.
            if (strcasecmp(substr($redirect_uri, 0, strlen($stored)), $stored) !== 0) {
                throw new InvalidRequestException();
            }
        }

        return $redirect_uri ? $redirect_uri : $stored;
    }
}

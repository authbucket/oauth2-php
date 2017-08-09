<?php


namespace AuthBucket\OAuth2\Symfony\Component\Security\Http\EntryPoint;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class TokenAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(['error'=>'invalid_request', 'error_message' => 'Client id and secret required'], 401);
    }
}

<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\Security\Authentication\Provider;

use AuthBucket\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ResourceProviderTest extends WebTestCase
{
    public function testNonCompatibileScope()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', 'bcc105b66698a64ed23c87b967885289']),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/resource/model', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_scope', $resourceResponse['error']);
    }

    public function testEnoughScope()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93']),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/resource/model', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $resourceResponse['username']);
    }

    public function testMoreScope()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', 'ba2e8d1f54ed3e3d96935796576f1a06']),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/resource/model', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $resourceResponse['username']);
    }
}

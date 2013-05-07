<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\Entity;

use Pantarei\Oauth2\Tests\Entity\Clients;
use Pantarei\Oauth2\Tests\Oauth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientsTest extends Oauth2_Database_TestCase
{
  public function testFind()
  {
    $clientRepository = $this->em->getRepository('Pantarei\Oauth2\Tests\Entity\Clients');
    $client = $clientRepository->find(1);

    $this->assertTrue($client !== NULL);
    $this->assertEquals('http://democlient1.com/', $client->getClientId());
    $this->assertEquals('demosecret1', $client->getClientSecret());
    $this->assertEquals('http://democlient1.com/redirect', $client->getRedirectUri());
  }
}

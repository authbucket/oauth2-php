<?php

namespace Pantarei\Oauth2\Test;

class AutoloadTest extends \PHPUnit_Framework_TestCase
{
  public function testExceptionClassExist()
  {
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\AccessDeniedException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\InvalidClientException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\InvalidGrantException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\InvalidRequestException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\InvalidScopeException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\ServerErrorException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\TemporarilyUnavailableException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\UnauthorizedClientException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\UnsupportedGrantTypeException'));
    $this->assertTrue(class_exists('Pantarei\Oauth2\Exception\UnsupportedResponseType'));
  }
}

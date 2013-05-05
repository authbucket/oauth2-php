<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Request;

use Pantarei\Oauth2\Oauth2;
use Pantarei\Oauth2\Exception\InvalidClientException;

/**
 * Authorization request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationRequest implements RequestInterface
{
  public function validateRequest(array $query = array())
  {
    $filters = array(
      "response_type" => array("filter" => FILTER_VALIDATE_REGEXP, "options" => array("regexp" => Oauth2::getRegexp('response_type')), "flags" => FILTER_REQUIRE_SCALAR),
      "client_id" => array("filter" => FILTER_VALIDATE_REGEXP, "options" => array("regexp" => Oauth2::getRegexp('client_id')), "flags" => FILTER_REQUIRE_SCALAR),
      "redirect_uri" => array("filter" => FILTER_SANITIZE_URL),
      "scope" => array("filter" => FILTER_VALIDATE_REGEXP, "options" => array("regexp" => Oauth2::getRegexp('scope')), "flags" => FILTER_REQUIRE_SCALAR),
      "state" => array("filter" => FILTER_VALIDATE_REGEXP, "options" => array("regexp" => Oauth2::getRegexp('state')), "flags" => FILTER_REQUIRE_SCALAR),
    );

    $input = filter_var_array($query, $filters);

    if (!$input['client_id']) {
      throw new InvalidClientException();
    }

    return TRUE;
  }
}

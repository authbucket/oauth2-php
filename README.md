AuthBucket\\OAuth2
==================

[![Build Status](https://travis-ci.org/authbucket/oauth2.svg?branch=master)](https://travis-ci.org/authbucket/oauth2)
[![Coverage Status](https://coveralls.io/repos/authbucket/oauth2/badge.png?branch=master)](https://coveralls.io/r/authbucket/oauth2?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5338d5457bae4b06600000b7/badge.svg)](https://www.versioneye.com/user/projects/5338d5457bae4b06600000b7)
[![Latest Stable Version](https://poser.pugx.org/authbucket/oauth2/v/stable.png)](https://packagist.org/packages/authbucket/oauth2)
[![Total Downloads](https://poser.pugx.org/authbucket/oauth2/downloads.png)](https://packagist.org/packages/authbucket/oauth2)
[![License](https://poser.pugx.org/authbucket/oauth2/license.png)](https://packagist.org/packages/authbucket/oauth2)

The primary goal of
[AuthBucket\\OAuth2](https://github.com/authbucket/oauth2) is to develop
a standards compliant [RFC6749
OAuth2.0](http://tools.ietf.org/html/rfc6749) library; secondary goal
would be develop corresponding wrapper [Symfony2
Bundle](http://www.symfony.com) and [Drupal module](http://drupal.org).

Installation
------------

This library is provided as a [Composer
package](https://packagist.org/packages/authbucket/oauth2) which cna be
installed by adding the package to your `composer.json`:

    {
      "require": {
        "authbucket/oauth2": "1.0.*@dev"
      }
    }

Documentation
-------------

The automatically generated [Sami](https://github.com/fabpot/Sami) can
be found from http://authbucket.github.io/oauth2.

If you hope to build the document locally, please execute
`sami.php update app/config/sami.php` and it will goes to `build/oauth2`
folder.

Continuous Integration
----------------------

This project is coverage with phpunit test cases, where CI result can be
found from https://travis-ci.org/authbucket/oauth2.

Code coverage CI result can be found from
https://coveralls.io/r/authbucket/oauth2.

If you hope to run the test cases locally, please execute
`phpunit -c phpunit.xml.dist`. Coverage report can be found from
`build/logs/html` folder.

References
----------

-   http://authbucket.github.io/oauth2/
-   https://coveralls.io/r/authbucket/oauth2
-   https://github.com/authbucket/oauth2
-   https://packagist.org/packages/authbucket/oauth2
-   https://travis-ci.org/authbucket/oauth2

License
-------

-   The library is licensed under the [MIT
    License](http://opensource.org/licenses/MIT)


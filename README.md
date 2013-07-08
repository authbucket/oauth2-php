PantaRei\OAuth2
===============

[![Build
Status](https://travis-ci.org/pantarei/oauth2.png?branch=master)](https://travis-ci.org/pantarei/oauth2)
[![Coverage
Status](https://coveralls.io/repos/pantarei/oauth2/badge.png?branch=master)](https://coveralls.io/r/pantarei/oauth2?branch=master)
[![Latest Stable
Version](https://poser.pugx.org/pantarei/oauth2/v/stable.png)](https://packagist.org/packages/pantarei/oauth2)
[![Total
Downloads](https://poser.pugx.org/pantarei/oauth2/downloads.png)](https://packagist.org/packages/pantarei/oauth2)

The primary goal of
[PantaRei\OAuth2](https://github.com/pantarei/oauth2) is to develop a
standards compliant [RFC6749
OAuth2.0](http://tools.ietf.org/html/rfc6749) library; secondary goal
would be develop corresponding wrapper [Symfony2
Bundle](http://www.symfony.com) and [Drupal module](http://drupal.org).

Installation
------------

This library is provided as a [Composer
package](https://packagist.org/packages/pantarei/oauth2) which cna be
installed by adding the package to your `composer.json`:

    {
      "require": {
        "pantarei/oauth2": "1.0.*@dev"
      }
    }

Documentation
-------------

The automatically generated doxygen can be found from
http://pantarei.github.io/oauth2.

If you hope to build the document locally, please execute
`doxygen config.doxygen` and it will goes to `build/_gh_pages` folder.

Continuous Integration
----------------------

This project is coverage with phpunit test cases, where CI result can be
found from https://travis-ci.org/pantarei/oauth2.

Code coverage CI result can be found from
https://coveralls.io/r/pantarei/oauth2.

If you hope to run the test cases locally, please execute
`phpunit -c phpunit.xml.dist`. Coverage report can be found from
`build/logs/html` folder.

References
----------

-   http://pantarei.github.io/oauth2/
-   https://coveralls.io/r/pantarei/oauth2
-   https://github.com/pantarei/oauth2
-   https://packagist.org/packages/pantarei/oauth2
-   https://travis-ci.org/pantarei/oauth2

License
-------

-   The library is licensed under the [MIT
    License](http://opensource.org/licenses/MIT)


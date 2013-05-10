Pantarei/OAuth2
===============

[![Build
Status](https://travis-ci.org/pantarei/oauth2.png?branch=master)](https://travis-ci.org/pantarei/oauth2)
[![Coverage
Status](https://coveralls.io/repos/pantarei/oauth2/badge.png)](https://coveralls.io/r/pantarei/oauth2)
[![Dependency
Status](https://www.versioneye.com/package/php--pantarei--oauth2/badge.png)](https://www.versioneye.com/package/php--pantarei--oauth2)

The primary goal of
[Pantarei/OAuth2](https://github.com/pantarei/oauth2) is to develop a
standards compliant [RFC6749
OAuth2.0](http://tools.ietf.org/html/rfc6749) library; secondary goal
would be develop corresponding wrapper [Symfony2
Bundle](http://www.symfony.com) and [Drupal module](http://drupal.org).

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
`build/html` folder.

Installation
------------

First you need to add `pantarei/oauth2` to `composer.json`:

    {
      "require": {
        "pantarei/oauth2": "1.0.*@dev"
      }
    }

License
-------

-   The bundle is licensed under the [MIT
    License](http://opensource.org/licenses/MIT)
-   The CSS and Javascript from the Twitter Bootstrap are licensed under
    the [Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0)


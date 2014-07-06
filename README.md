OAuth2 {#oauth2 .page-header}
======

[![Build
Status](https://travis-ci.org/authbucket/oauth2.svg?branch=master)](https://travis-ci.org/authbucket/oauth2)
[![Coverage
Status](https://img.shields.io/coveralls/authbucket/oauth2.svg)](https://coveralls.io/r/authbucket/oauth2?branch=master)
[![Dependency
Status](https://www.versioneye.com/php/authbucket:oauth2/dev-master/badge.svg)](https://www.versioneye.com/php/authbucket:oauth2/dev-master)
[![Latest Stable
Version](https://poser.pugx.org/authbucket/oauth2/v/stable.png)](https://packagist.org/packages/authbucket/oauth2)
[![Total
Downloads](https://poser.pugx.org/authbucket/oauth2/downloads.png)](https://packagist.org/packages/authbucket/oauth2)
[![License](https://poser.pugx.org/authbucket/oauth2/license.png)](https://packagist.org/packages/authbucket/oauth2)

The primary goal of
[AuthBucket\\OAuth2](https://github.com/authbucket/oauth2) is to develop
a standards compliant [RFC6749
OAuth2.0](http://tools.ietf.org/html/rfc6749) library; secondary goal
would be develop corresponding wrapper [Symfony2
Bundle](http://www.symfony.com) and [Drupal module](http://drupal.org).

Installation {#installation .page-header}
------------

Simply add a dependency on `authbucket/oauth2` to your project's
`composer.json` file if you use [Composer](http://getcomposer.org/) to
manage the dependencies of your project.

Here is a minimal example of a `composer.json`:

    {
        "require": {
            "authbucket/oauth2": "1.0.*@dev"
        }
    }

Demo {#demo .page-header}
----

This library bundle with a [Silex](http://silex.sensiolabs.org/) based
[OAuth2ServiceProvider](https://github.com/authbucket/oauth2/blob/master/src/AuthBucket/OAuth2/Provider/OAuth2ServiceProvider.php).
Read though [Demo](http://oauth2.authbucket.com/demo) for more
information.

You may also run the demo locally. Open a console and execute the
following command to install the latest version in the oauth2/
directory:

    $ composer create-project authbucket/oauth2 oauth2/ dev-master

Then use the PHP built-in web server to run the demo application:

    $ cd oauth2/
    $ php app/console server:run

If you get the error
`There are no commands defined in the "server" namespace.`, then you are
probably using PHP 5.3. That's ok! But the built-in web server is only
available for PHP 5.4.0 or higher. If you have an older version of PHP
or if you prefer a traditional web server such as Apache or Nginx, read
the [Configuring a web
server](http://silex.sensiolabs.org/doc/web_servers.html) article.

Open your browser and access the <http://localhost:8000> URL to see the
Welcome page of demo application.

Also access <http://localhost:8000/admin/refresh_database> to initialize
the bundled SQLite database with user account `admin`:`secrete`.

Documentation {#documentation .page-header}
-------------

OAuth2's documentation is built with
[Sami](https://github.com/fabpot/Sami) and publicly hosted on [GitHub
Pages](http://authbucket.github.io/oauth2).

To built the documents locally, execute the following command:

    $ vendor/bin/sami.php update app/config/sami.php

Open `build/oauth2/index.html` with your browser for the documents.

Tests {#tests .page-header}
-----

This project is coverage with [PHPUnit](http://phpunit.de/) test cases;
CI result can be found from [Travis
CI](https://travis-ci.org/authbucket/oauth2); code coverage report can
be found from [Coveralls](https://coveralls.io/r/authbucket/oauth2).

To run the test suite locally, execute the following command:

    $ vendor/bin/phpunit

Open `build/logs/html` with your browser for the coverage report.

References {#references .page-header}
----------

-   [RFC6749 OAuth2.0](http://tools.ietf.org/html/rfc6749)
-   [GitHub](https://github.com/authbucket/oauth2)
-   [Packagist](https://packagist.org/packages/authbucket/oauth2)
-   [Demo](http://oauth2.authbucket.com/demo)
-   [API](http://authbucket.github.io/oauth2/)
-   [Travis CI](https://travis-ci.org/authbucket/oauth2)
-   [Coveralls](https://coveralls.io/r/authbucket/oauth2)

License {#license .page-header}
-------

-   The library is licensed under the [MIT
    License](http://opensource.org/licenses/MIT)


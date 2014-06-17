AuthBucket\\OAuth2
==================

[![Build
Status](https://travis-ci.org/authbucket/oauth2.svg?branch=master)](https://travis-ci.org/authbucket/oauth2)
[![Coverage
Status](https://coveralls.io/repos/authbucket/oauth2/badge.png?branch=master)](https://coveralls.io/r/authbucket/oauth2?branch=master)
[![Dependency
Status](https://www.versioneye.com/user/projects/5338d5457bae4b06600000b7/badge.svg)](https://www.versioneye.com/user/projects/5338d5457bae4b06600000b7)
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

Demo Application
----------------

This library also bundle with a demo application that can be run
locally. Open a console and execute the following command to install the
latest version in the oauth2/ directory:

    $ composer create-project authbucket/oauth2 oauth2/ dev-master

Then use the PHP built-in web server to run the demo application:

    $ cd oauth2/
    $ php app/console server:run

If you get the error There are no commands defined in the "server"
namespace., then you are probably using PHP 5.3. That's ok! But the
built-in web server is only available for PHP 5.4.0 or higher. If you
have an older version of PHP or if you prefer a traditional web server
such as Apache or Nginx, read the [Configuring a web
server](http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html)
article.

Open your browser and access the http://localhost:8000 URL to see the
Welcome page of demo application.

Documentation
-------------

OAuth2's documentation is built with
[Sami](https://github.com/fabpot/Sami) and publicly hosted on GitHub
Pages at http://authbucket.github.io/oauth2. The docs may also built
locally.

### Built documentation locally

-   If necessary, [install
    Sami](https://github.com/fabpot/Sami#installation).
-   From the root directory, run `sami.php update app/config/sami.php`
    in the command line.
-   Open `build/oauth2/index.html` in your browser.

Learn more about using Sami by reading its
[documentation](https://github.com/fabpot/Sami/blob/master/README.rst).

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


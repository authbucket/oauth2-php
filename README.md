Pantarei/Oauth2
===============

[![Build
Status](https://travis-ci.org/pantarei/oauth2.png?branch=1.0)](https://travis-ci.org/pantarei/oauth2)

The primary goal of
[Pantarei/Oauth2](https://github.com/pantarei/oauth2) is to develop a
standards compliant [RFC6749
Oauth2.0](http://tools.ietf.org/html/rfc6749) library; secondary goal
would be develop corresponding wrapper [Symfony2
Bundle](http://www.symfony.com) and [Drupal module](http://drupal.org).

Documentation
-------------

The automatically generated doxygen can be found from
http://pantarei.github.io/oauth2.

If you hope to build the document locally, please execute
`doxygen config.doxygen` and it will goes to `_gh_pages` folder.

Testing
-------

This project is coverage with phpunit test cases, where CI result can be
found from https://travis-ci.org/pantarei/oauth2.

If you hope to run the test cases locally, please execute
`phpunit -c phpunit.xml.dist`.

Installation
------------

First you need to add `pantarei/oauth2` to `composer.json`:

    {
      "require": {
        "pantarei/bootstrap-bundle": "1.0.*@dev"
      }
    }

You also have to add `Oauth2` to your `AppKernel.php`:

    class AppKernel extends Kernel
    {
      public function registerBundles()
      {
      $bundles = array(
          new Pantarei\Oauth2\Oauth2()
          );
        return $bundles;
      }
    }

Assets
------

Since you are probably already using Composer this is the easiest way to
get started. Update your `composer.json` file and execute the following
line: `composer update`:

    {
      "require": {
        "twitter/bootstrap": "dev-3.0.0-wip"
      }
    }

### Without Assetic

Create symlink for the asset files from the `vendor/twitter/bootstrap`
directory into your web directory:

    mkdir -p web/bundles/twitter
    cd web/bundles/twitter
    ln -s ../../../vendor/twitter/bootstrap bootstrap

Now you can include boostrap css and js in main template:

    <link rel="stylesheet" href="{{ asset('bundles/twitter/bootstrap/docs/assets/css/bootstrap.css') }}">
    <script src="{{ asset('bundles/twitter/bootstrap/docs/assets/js/jquery.js') }}"></script>
    <script src="{{ asset('bundles/twitter/bootstrap/docs/assets/js/bootstrap.min.js') }}"></script>

### With Assetic

If you want to use LessPHP to compile the Bootstrap LESS files, you need
update your `composer.json` file and execute the following line:
`composer update`:

    {
      "require": {
        "leafo/lessphp": "0.3.9"
      }
    }

Now change your `app/config/config.yml` to this:

    # Assetic Configuration
    assetic:
      filters:
        lessphp:
          file: %kernel.root_dir%/../vendor/leafo/lessphp/lessc.inc.php
          apply_to: "\.less$"

After that, the last thing we need is to include bootstrap in main
template:

    {% stylesheets
      'bundles/twitter/bootstrap/less/*.less'
    %}
      <link rel="styleshet" href="{{ asset_url }} "/>
    {% endstylesheets %}

Examples
--------

If you hope to enable the examples as reference, update your
`app/config/routing.yml` file to this:

    pantarei_bootstrap:
        resource: "@Oauth2/Resources/config/routing.yml"
        prefix:   /_bootstrap

Then you can access `_bootstrap/starter-template` or other pages as
example.

License
-------

-   The bundle is licensed under the [MIT
    License](http://opensource.org/licenses/MIT)
-   The CSS and Javascript from the Twitter Bootstrap are licensed under
    the [Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0)


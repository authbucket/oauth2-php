# PantareiBootstrapBundle [![Build Status](https://travis-ci.org/pantarei/oauth2-bundle.png?branch=1.0)](https://travis-ci.org/pantarei/oauth2-bundle)

PantaReiBootstrapBundle is a collection of code to integrate Twitter Bootstrap
(http://twitter.github.com/bootstrap/) as easy as possible into your Symfony
(http://www.symfony.com) Project.

## Installation

First you need to add `pantarei/bootstrap-bundle` to `composer.json`:

    {
      "require": {
        "pantarei/bootstrap-bundle": "3.0.*@dev"
      }
    }

You also have to add `PantareiBootstrapBundle` to your `AppKernel.php`:

    class AppKernel extends Kernel
    {
      public function registerBundles()
      {
      $bundles = array(
          new Pantarei\Bundle\BootstrapBundle\PantareiBootstrapBundle()
          );
        return $bundles;
      }
    }

## Assets

Since you are probably already using Composer this is the easiest way to get started. Update your `composer.json` file and execute the following line: `composer update`:

    {
      "require": {
        "twitter/bootstrap": "dev-3.0.0-wip"
      }
    }

### Without Assetic

Create symlink for the asset files from the `vendor/twitter/bootstrap` directory into your web directory:

    mkdir -p web/bundles/twitter
    cd web/bundles/twitter
    ln -s ../../../vendor/twitter/bootstrap bootstrap

Now you can include boostrap css and js in main template:

    <link rel="stylesheet" href="{{ asset('bundles/twitter/bootstrap/docs/assets/css/bootstrap.css') }}">
    <script src="{{ asset('bundles/twitter/bootstrap/docs/assets/js/jquery.js') }}"></script>
    <script src="{{ asset('bundles/twitter/bootstrap/docs/assets/js/bootstrap.min.js') }}"></script>

### With Assetic

If you want to use LessPHP to compile the Bootstrap LESS files, you need update your `composer.json` file and execute the following line: `composer update`:

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

After that, the last thing we need is to include bootstrap in main template:

    {% stylesheets
      'bundles/twitter/bootstrap/less/*.less'
    %}
      <link rel="styleshet" href="{{ asset_url }} "/>
    {% endstylesheets %}

## Examples

If you hope to enable the examples as reference, update your `app/config/routing.yml` file to this:

    pantarei_bootstrap:
        resource: "@PantareiBootstrapBundle/Resources/config/routing.yml"
        prefix:   /_bootstrap

Then you can access `_bootstrap/starter-template` or other pages as example.

## License

- The bundle is licensed under the [MIT License](http://opensource.org/licenses/MIT)
- The CSS and Javascript from the Twitter Bootstrap are licensed under the [Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0)

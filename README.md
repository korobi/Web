Web
========================

Copyright &copy; 2014 - 2015

Staging
-------

A development instance is available at https://dev.korobi.io/. Authentication is required via the X-Korobi-Auth header (see app_dev.php in the web directory for more information).

Useful commands
---------------

**Run local testing server**
`php app/console server:start [0.0.0.0:8000] # Host:port optional`

**Create database (skip if already made)**
`php app/console doctrine:database:create`

**Run migrations**
`php app/console doctrine:mongodb:schema:create`

**Create document getters/setters**
`php app/console doctrine:mongodb:generate:documents KorobiWebBundle`

**Generate database hydrators**
`php app/console doctrine:mongodb:generate:hydrators`

**More information**
`php app/console doctrine:mongodb help`

The items below are mostly based on guess-work:

**Compile assets**
`php app/console assetic:dump`

**Watch assets and auto-compile**
`php app/console assetic:watch`

**Run tests**

NB: Tests are in the src directory but the PHPUnit config is in the app directory.

`cd app/; phpunit`

Requirements
------------
  * PHP 5.6+
  * SASS
  * node.js
  * uglify-js (npm)

What's inside?
--------------
  * Twig template engine;

  * Doctrine ORM/DBAL;

  * Swiftmailer;

  * Annotations enabled for everything.

  * **FrameworkBundle** - The core Symfony framework bundle

  * [**SensioFrameworkExtraBundle**][6] - Adds several enhancements, including
    template and routing annotation capability

  * [**DoctrineBundle**][7] - Adds support for the Doctrine ORM

  * [**TwigBundle**][8] - Adds support for the Twig templating engine

  * [**SecurityBundle**][9] - Adds security by integrating Symfony's security
    component

  * [**SwiftmailerBundle**][10] - Adds support for Swiftmailer, a library for
    sending emails

  * [**MonologBundle**][11] - Adds support for Monolog, a logging library

  * [**AsseticBundle**][12] - Adds support for Assetic, an asset processing
    library

  * **WebProfilerBundle** (in dev/test env) - Adds profiling functionality and
    the web debug toolbar

  * **SensioDistributionBundle** (in dev/test env) - Adds functionality for
    configuring and working with Symfony distributions

  * [**SensioGeneratorBundle**][13] (in dev/test env) - Adds code generation
    capabilities

[1]:  http://symfony.com/doc/2.6/book/installation.html
[6]:  http://symfony.com/doc/2.6/bundles/SensioFrameworkExtraBundle/index.html
[7]:  http://symfony.com/doc/2.6/book/doctrine.html
[8]:  http://symfony.com/doc/2.6/book/templating.html
[9]:  http://symfony.com/doc/2.6/book/security.html
[10]: http://symfony.com/doc/2.6/cookbook/email.html
[11]: http://symfony.com/doc/2.6/cookbook/logging/monolog.html
[12]: http://symfony.com/doc/2.6/cookbook/assetic/asset_management.html
[13]: http://symfony.com/doc/2.6/bundles/SensioGeneratorBundle/index.html

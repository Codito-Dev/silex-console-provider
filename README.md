codito/silex-console-provider
=============================

Provides a console application for Silex, built on top of `Symfony\Component\Console\Application`.

Requirements
------------

 * PHP >= 5.4 because of short array syntax.

Installation
------------

Add entries to `composer.json`:

```json
"require": {
	"codito/silex-console-provider": "~0.1"
}
```

Or run `composer require codito/silex-console-provider ~0.1` in your project directory.

Configuration
-------------

In your Silex bootstrap file you need to register service provider:

```php
<?php

use Codito\Silex\Provider\ConsoleServiceProvider;

$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'Silex CLI Application',
    'console.version'           => '1.0',
));

?>
```

Then in your console's script you can register pre-defined useful commands:

```php
<?php

/* @var $console \Codito\Silex\Console\Application */
$console = $app['console'];

$console->add(new \Codito\Silex\Console\Command\ClearCacheCommand());
$console->add(new \Codito\Silex\Console\Command\RouterDebugCommand());

?>
```

For more information about Symfony console, which is base for this console,
[look here](http://symfony.com/doc/current/components/console/introduction.html).

Usage
-----

Let's assume you have an `bin` directory and the `console` executable inside.
Calling `bin/console` will display list of available commands. Then you can call any of them like:

```
$ bin/console your:command:name
```

In order to use `cache:clear` command `cache_dir` must be defined in application, for example:

```
$app['cache_dir'] = __DIR__ . '/../var/cache';
```

Write commands
--------------

Your commands should extend `Codito\Silex\Console\Command\AbstractCommand` which is base (abstract)
class with handy method `getSilexApplication()`, which returns the Silex application,
where console service was registered.

Credits
-------

* Silex console application service provider was inspired by [`knplabs/console-service-provider`](https://github.com/KnpLabs/ConsoleServiceProvider)
* `router:debug` command is based on command available in Symfony [FrameworkBundle](https://github.com/symfony/framework-bundle) and was only adapted to use with Silex.

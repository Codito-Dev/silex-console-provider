<?php

namespace Codito\Silex\Provider;

use Codito\Silex\Console\Application as ConsoleApplication;
use Codito\Silex\Console\Helper\DescriptorHelper;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * ConsoleServiceProvider
 *
 * @author Grzegorz Korba <grzegorz.korba@codito.net>
 * @copyright 2015 Codito.net
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers service in Silex application
     * 
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['console'] = $app->share(function() use ($app) {
            $console = new ConsoleApplication(
                $app,
                $app['console.name'],
                $app['console.version']
            );

            $console->getHelperSet()->set(new DescriptorHelper(), 'descriptor');

            return $console;
        });
    }

    /**
     * Boots console service when all services were registered and main app boots up.
     * 
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
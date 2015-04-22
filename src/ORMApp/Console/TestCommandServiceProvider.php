<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 4/21/15
 * Time: 9:42 PM
 */

namespace ORMApp\Console;


use Silex\Application;
use Silex\ServiceProviderInterface;

class TestCommandServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        // noop
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        if (php_sapi_name() === 'cli') {
            $app['console.commands'] = $app->extend('console.commands', function ($commands) use ($app) {
                return array_merge(
                    $commands,
                    [
                        new TestCommand()
                    ]
                );
            });
        }
    }
}
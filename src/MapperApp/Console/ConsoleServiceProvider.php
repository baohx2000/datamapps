<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 4/25/15
 * Time: 6:33 PM
 */

namespace MapperApp\Console;


use Silex\Application;
use Silex\ServiceProviderInterface;

class ConsoleServiceProvider implements ServiceProviderInterface
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
        /** @var \Symfony\Component\Console\Application $console */
        $console = $app['console'];
        $console->add(new TestCommand(
            null,
            $app['person.mapper'],
            $app['phone.mapper'],
            $app['address.mapper']
        ));
    }
}
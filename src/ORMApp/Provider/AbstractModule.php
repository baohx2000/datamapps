<?php

namespace ORMApp\Provider;

use ORMApp\BaseApplication;
use ORMApp\InstanceApplication;
use ORMApp\Console\ConsoleLoadedEvent;
use Silex\Application;
use Silex\ServiceProviderInterface;

class AbstractModule implements ServiceProviderInterface {

    /**
     * Override this array to declare any service dependencies for your modules
     *
     * @var array
     */
    protected $services;

    /**
     * Override this array to declare any CLI commands exposed by your modules
     *
     * @var array
     */
    protected $cliCommands;

    /**
     * Override this array to declare any CLI commands exposed in development mode only
     * @var array
     */
    protected $cliCommandsDevOnly;

    public function addConfiguredServices($services) {
        if (!is_array($services)) return;

        foreach ($services as $service => $val) {
            if (isset($this[$service])) {
                unset($this[$service]);
            }
            $this[$service] = function() use ($val) {
                return $this->createInvokable($val);
            };
        }
    }

    private function createInvokable($service) {
        if (strpos($service, '::') !== false) {
            $instance = call_user_func($service, $this);
        } else {
            $instance = new $service();
        }

        return $instance;

    }

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        if ($app instanceof InstanceApplication) {
            $app->addConfiguredServices($this->services);
        }

        if (is_array($this->workerQueues)) {
            $queues =& $app[BaseApplication::CONFIG_KEY]['contatta.configuration']['worker.queues'];
            array_walk($this->workerQueues, function($queue) use(&$queues) {
                $queues[] = $queue;
            });
        }

        if (is_array($this->doctrineEventSubscribers)) {
            $subscribers =& $app[BaseApplication::CONFIG_KEY]['doctrine']['eventmanager'][InstanceDataAccessService::CONFIG_KEY]['subscribers'];
            array_walk($this->doctrineEventSubscribers, function($subscriber) use(&$subscribers) {
                $subscribers[] = $subscriber;
            });
        }
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registers
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app) {
        if (PHP_SAPI === 'cli' && is_array($this->cliCommands) || is_array($this->cliCommandsDevOnly)) {
            $app->on(ConsoleLoadedEvent::EVENT_CLI_LOADED, function(ConsoleLoadedEvent $e) use($app) {

                if (is_array($this->cliCommands)) {
                    array_walk($this->cliCommands, function ($class) use ($e) {
                        $e->cli->add(new $class);
                    });
                }

                if (APPLICATION_ENV === 'development') {
                    if (is_array($this->cliCommandsDevOnly)) {
                        array_walk($this->cliCommandsDevOnly, function ($class) use ($e) {
                            $e->cli->add(new $class);
                        });
                    }
                }

                $this->consoleLoaded($e->cli);
            });
        }
    }

    /**
     * Perform any other actions after the console has been loaded, but before being executed
     *
     * @param \Symfony\Component\Console\Application $cli
     */
    protected function consoleLoaded(\Symfony\Component\Console\Application $cli) {
    }
}

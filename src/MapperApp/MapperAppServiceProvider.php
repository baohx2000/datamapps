<?php
namespace MapperApp;


use MapperApp\Entities\AddressEntity;
use MapperApp\Entities\PersonEntity;
use MapperApp\Entities\PhoneEntity;
use MapperApp\Mappers\AddressMapper;
use MapperApp\Mappers\PersonMapper;
use MapperApp\Mappers\PhoneMapper;
use Silex\Application;
use Silex\ServiceProviderInterface;

class MapperAppServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['person.mapper'] = $app->share(function ($app) {
            return new PersonMapper($app['db'], new PersonEntity);
        });
        $app['phone.mapper'] = $app->share(function ($app) {
            return new PhoneMapper($app['db'], new PhoneEntity);
        });
        $app['address.mapper'] = $app->share(function ($app) {
            return new AddressMapper($app['db'], new AddressEntity);
        });
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
        // noop
    }
}

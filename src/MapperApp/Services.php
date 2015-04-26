<?php

namespace MapperApp;

use MapperApp\Console\ConsoleServiceProvider;
use Synapse\Application\ServicesInterface;
use Synapse\Application;

/**
 * Define services
 */
class Services implements ServicesInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app->register(new \Synapse\Command\CommandServiceProvider);
        $app->register(new \Synapse\Db\DbServiceProvider);
//        $app->register(new \Synapse\OAuth2\ServerServiceProvider);
//        $app->register(new \Synapse\OAuth2\SecurityServiceProvider);
//        $app->register(new \Synapse\Resque\ResqueServiceProvider);
        $app->register(new \Synapse\Controller\ControllerServiceProvider);
//        $app->register(new \Synapse\Email\EmailServiceProvider);
//        $app->register(new \Synapse\User\UserServiceProvider);
        $app->register(new \Synapse\Migration\MigrationServiceProvider);
//        $app->register(new \Synapse\Install\InstallServiceProvider);
        $app->register(new \Synapse\Security\SecurityServiceProvider);
        $app->register(new \Synapse\Session\SessionServiceProvider);
//        $app->register(new \Synapse\SocialLogin\SocialLoginServiceProvider);
        $app->register(new \Synapse\Time\TimeServiceProvider);
        $app->register(new \Synapse\Validator\ValidatorServiceProvider);

        $app->register(new \Synapse\View\ViewServiceProvider, [
            'mustache.paths' => array(
                APPDIR.'/templates'
            ),
            'mustache.options' => [
                'cache' => TMPDIR,
            ],
        ]);

//        $app->register(new \Silex\Provider\ValidatorServiceProvider);
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider);

        $app->register(new ConsoleServiceProvider);
        $app->register(new MapperAppServiceProvider);
    }
}

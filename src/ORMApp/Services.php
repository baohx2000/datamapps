<?php

namespace ORMApp;

use B2k\Doc\DocServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
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
//        $app->register(new \Synapse\Db\DbServiceProvider);
//        $app->register(new \Synapse\OAuth2\ServerServiceProvider);
//        $app->register(new \Synapse\OAuth2\SecurityServiceProvider);
//        $app->register(new \Synapse\Resque\ResqueServiceProvider);
        $app->register(new \Synapse\Controller\ControllerServiceProvider);
//        $app->register(new \Synapse\Email\EmailServiceProvider);
//        $app->register(new \Synapse\User\UserServiceProvider);
//        $app->register(new \Synapse\Migration\MigrationServiceProvider);
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

        $app->register(new DoctrineServiceProvider, [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'dbname' => 'datamapps',
                'user' => 'root',
                'host' => '127.0.0.1',
            ]
        ]);

        $app->register(new DoctrineOrmServiceProvider, [
            'orm.proxies_dir' => APPDIR.'/proxies',
            'orm.em.options' => [
                'mappings' => [
                    [
                        'type' => 'annotation',
                        'namespace' => 'ORMApp\Entities',
                        'resource_namespace' => 'ORMApp\Entities',
                    ]
                ]
            ]
        ]);

        $app->register(new DocServiceProvider());
    }
}

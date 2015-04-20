<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

defined('APPDIR') or define('APPDIR', realpath(__DIR__.'/..'));
defined('DATADIR') or define('DATADIR', APPDIR.'/data');
defined('TMPDIR') or define('TMPDIR', '/tmp');

// replace with file to your own project bootstrap
$app = require_once realpath(__DIR__.'/../src/ORMApp/').'/bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $app['entitymanager'];

return ConsoleRunner::createHelperSet($entityManager);

<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
$app = require_once '../src/ORMApp/bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $app['entitymanager'];

return ConsoleRunner::createHelperSet($entityManager);

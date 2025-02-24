<?php


use DI\Container;
use Slim\Factory\AppFactory;



require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

$container = new Container();
$dependencies = require __DIR__ . '/../config/dependencies.php';
$dependencies($container);

AppFactory::setContainer($container);

$app = AppFactory::create();

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();

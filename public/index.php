<?php


use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

$container = new Container();
$dependencies = require __DIR__ . '/../config/dependencies.php';
$dependencies($container);

AppFactory::setContainer($container);

$app = AppFactory::create();

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);


$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails) use ($app) {
        $response = $app->getResponseFactory()->createResponse();


        ob_start();
        require '../templates/404view.php';
        $html = ob_get_clean();


        $response->getBody()->write($html);
        return $response->withStatus(404);
    }
);

$app->run();

<?php

use DI\Container;
use Psr\Container\ContainerInterface;
use App\Controller\StudentController;
use App\Controller\GradesController;

return function (Container $container) {
    $container->set(PDO::class, function (ContainerInterface $ci) {
        return require __DIR__ . '/db.php';
    });
    $container->set(StudentController::class, function (ContainerInterface $ci) {
        return new StudentController($ci->get(PDO::class));
    });
    $container->set(GradesController::class, function ($container) {
        return new GradesController($container->get(PDO::class));
    });
};

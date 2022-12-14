<?php

/** @var Router $router */
global $router;

/** @var Container $container */
global $container;

use App\Controllers\{Auth\LoginController,
    Auth\LogoutController,
    Auth\RegisterController,
    CalendarController,
    HomeController};
use App\Middleware\{Authenticated, Guest};
use League\{Container\Container, Route\RouteGroup, Route\Router};

// Routes that need authentication in order to access
$router->group('', function (RouteGroup $router) {

    $router->get('/', [HomeController::class, 'index'])->setName('home');

    $router->get('/calendar', [CalendarController::class, 'index'])->setName('calendar');

//    $router->post('/calendar', [CalendarController::class, 'createAppointment']);
    $router->post('/makeAppointment', [CalendarController::class, 'createAppointment']);

    $router->post('/logout', [LogoutController::class, 'logout'])->setName('logout');

})->middleware($container->get(Authenticated::class));

// Routes that can be accessed only if the user is NOT authenticated
$router->group('', function (RouteGroup $router) {

//    $router->post('/calendar', [CalendarController::class, 'createAppointment']);

    $router->get('/login', [LoginController::class, 'index'])->setName('login');

    $router->post('/login', [LoginController::class, 'store'])->setName('login.store');

    $router->get('/register', [RegisterController::class, 'index'])->setName('register');
    //has a form for get

    $router->post('/register', [RegisterController::class, 'store'])->setName('register.store');
    //has a form for post

})->middleware($container->get(Guest::class));

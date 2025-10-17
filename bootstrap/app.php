<?php

use App\Http\Controllers\TaskController;
use App\Policies\TaskPolicy;
use Core\App;
use Core\Container;
use Core\Database;
use Core\Request;

$container = new Container();

$container->singleton('Core\Database', function () {

    $config = require basePath('config/database.php');

    return new Database($config);
});

$container->singleton('Core\Request', function () {
    return Request::capture();
});

$container->bind(TaskController::class, function () {
    return new TaskController(new TaskPolicy());
});

App::setContainer($container);

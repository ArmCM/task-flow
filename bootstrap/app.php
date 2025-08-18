<?php

use Core\App;
use Core\Container;
use Core\Database;
use Core\Request;

$container = new Container();

$container->singleton('Core\Database', function () {

    $config = require basePath('config/database.php');

    return new Database($config);
});

$container->bind('Core\Request', function () {
    return Request::capture();
});

App::setContainer($container);

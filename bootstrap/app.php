<?php

use Core\App;
use Core\Container;
use Core\Database;

$container = new Container();

$container->singleton('Core\Database', function () {

    $config = require basePath('config/database.php');

    return new Database($config);
});

App::setContainer($container);

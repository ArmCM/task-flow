<?php

use App\Http\Controllers\TaskController;

/** @var $router */

$router->get('/', function () {
    echo "root";
});

$router->get('/tasks', [TaskController::class, 'index']);

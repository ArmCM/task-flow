<?php

use App\Http\Controllers\TaskController;

/** @var $router */

$router->get('/tasks', [TaskController::class, 'index']);
